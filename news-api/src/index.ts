import * as Koa from 'koa';
import * as Router from 'koa-router';
import { Server } from 'http';
import * as bodyparser from 'koa-bodyparser';
import * as helmet from 'koa-helmet';
import { AddressInfo } from 'net';
import * as request from 'request-promise-native';
import * as qs from 'querystring';

const DEFAULT_HTTP_PORT = 3500;
const NEWS_API_KEY = '152633accf0243dcac0e3379c52cfb52';

async function init() {
	const app = new Koa();
	const router = new Router();

	app.use(bodyparser());
	app.use(helmet());

	// search for news route
	router.get('/news', async (ctx) => {
		const searchQuery = ctx.query.search;

		if (!searchQuery || !('string' === typeof searchQuery)) {
			ctx.throw(400);
		}

		const headers = {
			'X-Api-Key': NEWS_API_KEY
		};

		const url = `https://newsapi.org/v2/everything?${qs.stringify({ q: searchQuery, sortBy: 'publishedAt', sources:'the-new-york-times' })}`;

		const response = await request(url, {
			method: 'GET',
			headers,
			json: true,
		});

		if (!(response.status === 'ok')) {
			ctx.throw(400);
		}

		ctx.body = {
			success: true,
			searchResults: response.articles
		};
	});

	app.use(router.routes());

	// Default response
	app.use(ctx => ctx.body = 'Route not found');

	// And finally start the HTTP server
	const server: Server = await app.listen(DEFAULT_HTTP_PORT);

	console.log(`HTTP server listening on port ${(server.address() as AddressInfo).port}`);
};

init();

