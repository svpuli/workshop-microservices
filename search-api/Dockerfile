FROM node:10-alpine

ENV SERVICE_NAME=search-api \
	SERVICE_5000_NAME=search-api \
	SERVICE_DESC='An example of a micro-service that searches for news and gifs.' \
	SERVICE_TAGS='micro-service,search-api' \
	SERVICE_CHECK_HTTP=/health-check \
	SERVICE_CHECK_INTERVAL=15s \
	SERVICE__CHECK_TIMEOUT=2s  \
	SERVICE_5674_IGNORE=true

EXPOSE 5000 5674

WORKDIR /app

CMD ["node", "dist/index.js"]
