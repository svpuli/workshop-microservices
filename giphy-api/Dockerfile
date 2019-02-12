FROM node:10-alpine

ENV SERVICE_NAME=giphy-api \
	SERVICE_4000_NAME=giphy-api \
	SERVICE_DESC='An example of a micro-service that searches for gifs.' \
	SERVICE_TAGS='micro-service,giphy-api' \
	SERVICE_CHECK_HTTP=/health-check \
	SERVICE_CHECK_INTERVAL=15s \
	SERVICE__CHECK_TIMEOUT=2s  \
	SERVICE_5574_IGNORE=true

EXPOSE 4000 5574

WORKDIR /app

CMD ["/app/run.sh"]
