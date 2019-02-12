FROM node:10-alpine

ENV SERVICE_NAME=news-api \
	SERVICE_3500_NAME=news-api \
	SERVICE_DESC='An example of a micro-service that searches for news.' \
	SERVICE_TAGS='micro-service,news-api' \
	SERVICE_5474_IGNORE=true

EXPOSE 3500 5474

WORKDIR /usr/app

COPY package.json .

RUN npm install --quiet

COPY . .

CMD ["node", "dist/index.js"]
