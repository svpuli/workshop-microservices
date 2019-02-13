# Microservices - is that easy?

Welcome to the **Microservices - is that easy?** workshop.

The goal of this workshop is to provide a general overview about the development challenges and bennefits in a Microservices architecture.

**The workshop is devided in 2 main parts:**
 * Brief overview about Microservices and Docker - [slides](https://www.pipedrive.com)
 * Workshop - hands on:
    * [Decomposition](#part1)
    * [API gateway - limit exposure](#part2)
    * [Fault tolerance and Service configuration](#part3)
    * [Service discovery and Health check](#part4)

## System Requirements

In order to successfully follow the workshop, the following tools/services must be previously installed:
 * [Git](https://git-scm.com/downloads) v2.14.1 or greater
 * [Docker](https://docs.docker.com/install/) v18.09.1 or greater
 * [Node](https://nodejs.org/en/download/) v10.15.0 or greater
    * Node can also be installed via nvm - [Linux and MacOs instructions](https://gist.github.com/d2s/372b5943bce17b964a79) & [Windows instructions](https://github.com/coreybutler/nvm-windows)

All these should be available in your **PATH**. To verify things are set up properly, you can run this:

```sh
git --version
docker --version
node --version
```

## Setup

You will need to start by cloning this repo, then you'll just have to spin up all the docker services. From your terminal, type:

```sh
git clone git@github.com:pipedrive/workshop-sinfo.git
cd workshop-sinfo
docker-compose up
```

## 🤘 Time to start

### 1.Decomposition <a id="part1"/>

As we have seen in the _**Brief overview about Microservices and Docker**_, microservices should be built accordingly with its own business logical capability.

Currently `search-api` service is requesting both **news** and **gifs** directly to external providers (instead of making use of the `news-api` and `giphy-api` service).

This does not seem to be right as **news** and **gifs** are completely different things and as so might have completely different requirements.

**Tasks to do:**
* git checkout branch start-workshop
```sh
git checkout start-workshop
```
* Build the project
```sh
docker-compose up --build
```
* On your search-ui (on the browser access http://localhost:8080) and try search for _Trump_

* Change the `search-api` service to start requesting both the `news-api` and the `giphy-api` services.
    * Change `search-api` to request `news-api` and `giphy-api` instead of requesting the external services **newsapi.org** and **api.giphy.com**
    ```js
    // on index.ts replace the router Get /search by the following code

    const searchQuery = ctx.query.q;

	if (!searchQuery || !('string' === typeof searchQuery)) {
		ctx.throw(400);
	}

	const newsUrl = `http://news-api:3500/news?${qs.stringify({ search: searchQuery })}`;
	const gifsUrl = `http://giphy-api:4000/gifs?${qs.stringify({ search: searchQuery })}`;
	let newsResponse;
	let gifsResponse;

	try {
		newsResponse = await request(newsUrl, {
			method: 'GET',
			json: true,
		});
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
		news: newsResponse ?newsResponse.searchResults : [],
		gifs: gifsResponse ?gifsResponse.searchResults : []
	};
    ```

* On the search-ui try search for anything again. What are the results?

* Stop the docker containers (on the console with the docker logs attach just hit `ctrl c` or open a new terminal in the workshop-sinfo dir and run `docker-compose down`)

* Add the following configuration to the **docker-compose.yml**
```docker
  giphy-api:
    build: giphy-api/.
    ports:
      - 4000:4000
      - 5574:5574
    volumes:
      - ./giphy-api:/app/

  news-api:
    build: news-api/.
    ports:
      - 3500:3500
      - 5474:5474
    volumes:
      - ./news-api:/app/
```
* Rebuild the project
```sh
docker-compose up --build
```
* On the search-ui try search for anything again. What are the results now?

* Scale the `news-api` service to 2 instances and rebuild the project. Edit **docker-compose.yml** and add the following
    ```docker
    deploy:
        replicas: 2
    ```

### 2.API Gateway <a id="part2"/>

In a microservices architecture, ideally there should a be a single point of entry in order to access the back end services.

As so, we are going to change our current services so that the `search-api` service becomes our single point of entry -> ***news*** and ***gifs*** will be searched via `search-api`.

**Tasks to do:**
* git checkout branch api-gateway
```sh
git checkout api-gateway
```
* Rebuild the project
```sh
docker-compose up --build
```
* Access directly the `news-api` and `giphy-api` services and check that each one returns a valid response.
    * http://localhost:3500/news?search=<YOUR_SEARCH>
    * http://localhost:4000/gifs?search=<YOUR_SEARCH>

* Edit `docker-compose.yml` and remove the port mapping for `news-api` and `giphy-api`.
```docker
ports:
      - "5574:5574"
```
* Stop and rebuild the services
```sh
docker-compose down
docker-compose up --build
```

* Confirm that the previous endpoints are no longer accessible but that the you can still request the results through `search-api` (`http://localhost:5000/search?q=<YOUR_SEARCH>`) or using the `search-ui` app

### 3.Fault tolerance <a id="part3"/>

When a microservice is failing, the overall application whould be able to keep working (even if at a reduced level). Also redeploying a new version of a specific service should have zero impact on the overall application.

**Tasks to do:**
* git checkout branch fault-tolerance
```sh
git checkout fault-tolerance
```
* Rebuild the project
```sh
docker-compose up --build
```
* Stop one of the microservices (for example the `giphy-api`service) and confirm that the overall service keeps working even if no gifs are returned.

    _Stop giphy-api container:_
    ```sh
    docker stop $(docker ps -a -q --filter ancestor=workshop-sinfo_giphy-api)
    ```
    _Start giphy-api container:_

    ```sh
    docker start $(docker ps -a -q --filter ancestor=workshop-sinfo_giphy-api)
    ```

* Change `news-api` GET /news endpoint to only return news from everywhere (currently it is only retrieving news from the
_The New York Times_).
    * remove _**sources: 'the-new-york-times'**_ from the request url.
    ```js
    const url = `https://newsapi.org/v2/everything?${qs.stringify({ q: searchQuery })}`;
    ```

### 4.Service discover and health check <a id="part4"/>

In a microservices architecture, the number of services is intended to grow up to a level where it impossible to rely on manual configuration for services to communicate with each other.
Moreover services are constantly being scaled up and scaled down. This means that they will get available network IPs and assinged on the fly.

The solution is **service discovery** and we are going to use Consul for that.

**Tasks to do:**
* git checkout branch service-discovery
```sh
git checkout service-discovery
```
* Rebuild the project
```sh
docker-compose up --build
```
* Open Consul ui and see that all the services are registered there.
    * http://localhost:9500/ui/#/dc1/services/

* Add health check endpoint to all BE services (`news-api`, `giphy-api`, `search-api`).
    * Add the health-check route before any other route.
    ```js
    // health-check route
	router.get('/health-check', (ctx) => ctx.status = 200);
    ```

* Add health check configurations to each service **Dockerfile**
    *  Add the following env variables:
    ```docker
    SERVICE_CHECK_HTTP=/health-check \
    SERVICE_CHECK_INTERVAL=15s \
    SERVICE_CHECK_TIMEOUT=2s  \
    ```
    * Stop the docker containers and rebuild the project.
    ```sh
    docker-compose down
    docker-compose up --build
    ```

* Open Consul and check that all services are passing both on container health and service health check.
    * http://localhost:9500/ui/#/dc1/services/search-api

* Change the `giphy-api` health check reply with http code **503**. Wait a couple of seconds and check that the service is now failing on consul.
```js
ctx.status = 503;
```

---

💚 [Pipedrive](https://www.pipedrive.com) workshop

_Built by Pipedrive_