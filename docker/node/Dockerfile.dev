FROM node:20-alpine

WORKDIR /var/www/html

# Copy package.json & package-lock.json untuk caching
COPY package*.json ./

RUN npm install

# Copy seluruh source code
COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev"]
