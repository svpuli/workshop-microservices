import * as Koa from 'koa';
import * as Router from 'koa-router';
import { Server } from 'http';
import * as bodyparser from 'koa-bodyparser';
import * as helmet from 'koa-helmet';
import { AddressInfo } from 'net';
import * as request from 'request-promise-native';
import * as qs from 'querystring';

const DEFAULT_HTTP_PORT = 5000;

async function init() {
	const app = new Koa();
	const router = new Router();

	app.use(bodyparser());
	app.use(helmet());

	// health-check route
	router.get('/health-check', (ctx) => ctx.status = 200);

	router.get('/search', async (ctx: any, _next: any) => {
		const searchQuery = ctx.query.q;

		if (!searchQuery || !('string' === typeof searchQuery)) {
			ctx.throw(400);
		}

		const newsUrl = `http://news-api:3500/news?${qs.stringify({ search: searchQuery })}`;
		const gifsUrl = `http://giphy-api:4000/gifs?${qs.stringify({ search: searchQuery })}`;

		try {
			const newsResponse = await request(newsUrl, {
				method: 'GET',
				json: true,
			});

			const gifsResponse = await request(gifsUrl, {
				method: 'GET',
				json: true,
			});

			ctx.body = {
				success: true,
				news: newsResponse.searchResults,
				gifs: gifsResponse.searchResults
			};
		} catch (error) {
			console.log(error);
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

