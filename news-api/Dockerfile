FROM node:10-alpine

ENV SERVICE_NAME=news-api \
	SERVICE_3000_NAME=news-api \
	SERVICE_DESC='An example of a micro-service that searches for news.' \
	SERVICE_TAGS='micro-service,news-api' \
	SERVICE_CHECK_HTTP=/health-check \
	SERVICE_CHECK_INTERVAL=15s \
	SERVICE__CHECK_TIMEOUT=2s  \
	SERVICE_5474_IGNORE=true

EXPOSE 3000 5474

WORKDIR /app

CMD ["node", "dist/index.js"]
