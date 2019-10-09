# -*- coding: utf-8 -*-
# Для запуска программы необходимо скачать интерпретатор языка Python3.7 и установить его
# https://www.python.org/ftp/python/3.7.4/python-3.7.4.exe
# После этого необходимо иметь активное соединение с интернетом, 
# открыть командную строку от имени админиматратора и написать следущие комманды:
# pip install numpy
# pip install matplotlib
# pip install networkx
# После этого можно запускать программу
# На вход подаётся файл CSV (Первая строка и первый столбец - заголовки)
# На выход выдаётся список, ранжированный по алгоритму PageRank
import numpy as np
import random
import matplotlib.pyplot as plt
import networkx as nx



def v_len(arr):
    l = arr.shape[0]
    res = 0
    for i in range(l):
        res = res + (arr[i])**2
    res = res ** 0.5
    return res

def matvec(matrix, vector):
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
    while temp > depth:
        x1 = matvec(matrix, vector)
        x1 = x1 / np.sum(x1)
        temp = v_len(x1 - vector)
        print(temp)
        vector = x1
    #print(np.sum(x1))
    return x1

datafile = open('C:\\Users\\yuram\\Desktop\\project\\metro\\metro.csv', 'r')
lines = datafile.read().split(sep='\n')
N = len(lines)-2
elms = lines[0].split(sep=';')
DATA = np.zeros([N,N])
names = []
for i in range(N):
    print(i)
    elms = lines[i+1].split(sep=';')
    names.append(elms[0])
    for j in range(N):
        DATA[i,j] = float(elms[j+1])

G = nx.DiGraph(directed=True)
G= nx.from_numpy_matrix(DATA)
#print(DATA)
nx.draw_networkx(G)
#print(G.edges)
plt.show()

size = N
M = DATA
x0 = np.ones(size)/size
eps=1e-5
E = np.ones([size,size])
d = 0.0005 # https://en.wikipedia.org/wiki/PageRank#Power_Method (if we have hanging nodes)
E = (E * d)/size
M = (1 - d) * M + E
x1 = calculate_pages(M, x0, eps)
all = []
for i in range(N):
    a = []
    a.append(x1[i])
    a.append(names[i])
    all.append(a)
all = sorted(all, reverse=True)
for i in range(N):
    print("<tr><td>"+str(i+1)+")</td> <td>"+all[i][1]+"</td> <td>"+str(round((all[i][0]*100), 3))+"%</td></tr>")
input()