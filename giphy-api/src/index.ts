import * as Koa from 'koa';
import * as Router from 'koa-router';
import { Server } from 'http';
import * as bodyparser from 'koa-bodyparser';
import * as helmet from 'koa-helmet';
import { AddressInfo } from 'net';
import * as request from 'request-promise-native';
import * as qs from 'querystring';

const DEFAULT_HTTP_PORT = 4000;
const GIPHY_API_KEY = 'XUqFqVY3Bxwvvh9siipzWxNelAZjfhSa';


async function init() {
	const app = new Koa();
	const router = new Router();

	app.use(bodyparser());
	app.use(helmet());

	// health-check route
	router.get('/health-check', (ctx) => ctx.status = 200);

	router.get('/gifs', async (ctx: any, _next: any) => {
		const searchQuery = ctx.query.search;

		if (!searchQuery || !('string' === typeof searchQuery)) {
			ctx.throw(400);
		}

		const url = `https://api.giphy.com/v1/gifs/search?${qs.stringify({ q: searchQuery, api_key: GIPHY_API_KEY })}`;

		try {
			const response = await request(url, {
				method: 'GET',
				json: true,
			});

			ctx.body = {
				success: true,
				searchResults: response
			};
		} catch (error) {
			ctx.throw(error.statusCode);
		}
	});

	app.use(router.routes());

	// response
	app.use((ctx: any) => {
		ctx.body = 'Route not found';
	});

	// And finally start the HTTP server
	const server: Server = await app.listen(DEFAULT_HTTP_PORT);

	console.log(`HTTP server listening on port ${(server.address() as AddressInfo).port}`);
};

init();

