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

	router.get('/search', async (ctx: any, _next: any) => {
		const searchQuery = ctx.query.q;

		if (!searchQuery || !('string' === typeof searchQuery)) {
			ctx.throw(400);
		}

		const newsUrl = `https://newsapi.org/v2/everything?${qs.stringify({ q: searchQuery, sortBy: 'publishedAt', sources: 'the-new-york-times' })}`;
		const gifsUrl = `https://api.giphy.com/v1/gifs/search?${qs.stringify({ q: searchQuery, api_key: 'XUqFqVY3Bxwvvh9siipzWxNelAZjfhSa' })}`;
		let newsResponse;
		let gifsResponse;

		try {
			const headers = {
				'X-Api-Key': '152633accf0243dcac0e3379c52cfb52'
			};

			const response = await request(newsUrl, {
				method: 'GET',
				headers,
				json: true,
			});

			if (!(response.status === 'ok')) {
				ctx.throw(400);
			}

			newsResponse = response.articles;
		} catch (error) {
			console.log(error);
		}

		try {
			gifsResponse = await request(gifsUrl, {
				method: 'GET',
				json: true,
			});
		} catch (error) {
			console.log(error);
		}

		ctx.body = {
			success: true,
			news: newsResponse ? newsResponse : [],
			gifs: gifsResponse ? gifsResponse : []
		};
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
