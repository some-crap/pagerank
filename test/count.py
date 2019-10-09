# -*- coding: utf-8 -*-
import numpy as np
import random
import matplotlib.pyplot as plt
import networkx as nx
import time

def vector_len(arr):
    l = arr.shape[0]
    res = 0
    for i in range(l):
        res = res + (arr[i])**2
    res = res ** 0.5
    return res


def matrix_multiplication_by_vector(matrix, vector):
    l = matrix.shape[0]
    res = np.zeros(matrix.shape[0])
    for i in range(l):
        for n in range(matrix.shape[1]):
            res[i] = res[i]+matrix[i, n] * vector[n]
    return res


def matrix_gen(r):
    res = np.zeros([r, r])
    for i in range(r):
        for n in range(r):
            res[n, i] = random.randint(0, 1)
    for i in range(r):
        tmp = 0
        for n in range(r):
            if res[n, i] == 1:
                tmp += 1
        if tmp == 0:
            res[random.randint(0, r), i] = 1
        else:
            tmp = 1 / tmp
            for n in range(r):
                if res[n, i] == 1:
                    res[n, i] = tmp
    return res


def calculate_pages(matrix, vector, depth=1e-6):
    temp = 2
    count = 0
    while temp > depth:
        time1e = time.time()
        x1 = matrix_multiplication_by_vector(matrix, vector)
        x1 = x1 / np.sum(np.abs(x1))
        temp = vector_len(x1 - vector)
        vector = x1
        count += 1
        time2e = time.time()
        print(str(count) + ' iteration ' + str(time2e-time1e))
    print(count)
    return x1


print('ok')
i = 0
while i <= 5500:
    i += 500
    N = i
    print('N= '+str(N))
    DATA = matrix_gen(N)
    M = DATA
    time1 = time.time()
    size = N
    x0 = np.ones(size)/size
    eps = 1e-10
    E = np.ones([size,size])
    d = 0.0005 # https://en.wikipedia.org/wiki/PageRank#Power_Method (if we have hanging nodes)
    E = (E * d)/size
    M = (1 - d) * M + E
    x1 = calculate_pages(M, x0, eps)
    time2 = time.time()
    total = time2 - time1
    print(str(total) + 'Full cycle for N=' + str(N))
