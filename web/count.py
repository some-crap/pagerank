# -*- coding: utf-8 -*-
import numpy as np
import random
import os
from sys import argv


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


def matrix_gen(non_stochastic_matrix,N):
    res = non_stochastic_matrix
    for i in range(N-1):
        tmp = 0
        for n in range(N-1):
            tmp += res[n, i]
        if tmp != 0:
            tmp = 1 / tmp
        for n in range(N-1):
            res[n, i] = tmp * res[n, i]
    return res


def calculate_pages(matrix, vector, depth=1e-6):
    temp = 2
    count = 0
    while temp > depth:
        x1 = matrix_multiplication_by_vector(matrix, vector)
        x1 = x1 / np.sum(np.abs(x1))
        temp = vector_len(x1 - vector)
        #print(temp)
        vector = x1
        count += 1
    #print(np.sum(x1))
    print(count)
    return x1


filename = argv[1]
datafile = open(filename, 'r')
# reading CSV
lines = datafile.read().split(sep='\n')
N = len(lines)
lms = lines[0].split(sep=';')
DATA = np.zeros([N-1, N-1])
names = []
for i in range(N-1):
    elms = lines[i].split(sep=';')
    print(i)
    names.append(elms[0])
    for j in range(N-1):
        DATA[i, j] = float(elms[j+1])
# reading CSV
size = N-1
M = matrix_gen(DATA,N)
x0 = np.ones(size)/size
eps = 1e-10
E = np.ones([size,size])
d = 0.0005 # https://en.wikipedia.org/wiki/PageRank#Power_Method (if we have hanging nodes)
E = (E * d)/size
M = (1 - d) * M + E
x1 = calculate_pages(M, x0, eps)
all = []
for i in range(N-1):
    a = []
    a.append(x1[i])
    a.append(names[i])
    all.append(a)
all = sorted(all, reverse=True)
strin = ""
for i in range(N-1):
    strin = strin + str(i+1)+") "+all[i][1]+": "+str(round((all[i][0]*100), 2))+"%<br>"
print (strin)
#os.remove(filename)
