#!/bin/sh
# Compile ngsecurity as a shared library on Linux/macOS (requires g++/clang++).
cd "$(dirname "$0")"
g++ -std=c++17 -O2 -fPIC -shared -o ngsecurity.so ngsecurity.cpp -static-libgcc
echo "[OK] native/ngsecurity.so"
