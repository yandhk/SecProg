# Stage 1: build frontend assets
FROM node:22-alpine AS builder
WORKDIR /app

# Salin dependencies dan install
COPY package*.json ./
RUN npm ci

# Salin semua source code untuk build
COPY . .

# Jalankan build Vite
RUN npm run build
