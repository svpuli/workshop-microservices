# Microservices - it's that easy?

Welcome to the **Microservices - is that easy?** workshop.

The goal of this workshop is to provide a general overview about the development challenges and benefits in a Microservices architecture.

## System Requirements

In order to successfully follow the workshop, the following tools/services must be previously installed:
 * [Git](https://git-scm.com/downloads) v2.14.1 or greater
 * [Docker](https://docs.docker.com/install/) v18.09.1 or greater
 * [Docker Hub account](https://hub.docker.com/)
 * [Node](https://nodejs.org/en/download/) v10.15.0 or greater
    * Node can also be installed via nvm - [Linux and MacOs instructions](https://gist.github.com/d2s/372b5943bce17b964a79) & [Windows instructions](https://github.com/coreybutler/nvm-windows)

All of these should be available in your PATH. To verify everything is set up properly, you can run the following commands:

```sh
git --version
docker --version
node --version
```

## Setup

You will need to start by cloning this repo. From your terminal, type:

```sh
git clone git@github.com:pipedrive/workshop-sinfo.git
cd workshop-sinfo
```

## Workshop Structure

**The workshop is devided into two main parts:**
 * Brief overview about Microservices and Docker - [slides](https://docs.google.com/presentation/d/1ZSlxhAOA7ZHsjPPJjgg7_MyN3QZetIMH4gLYxyRQ7U8/edit?usp=sharing)
 * Workshop - hands on:
    - [1. Decomposition](#part1)
    - [2. API gateway - limit exposure](#part2)
    - [3. Fault tolerance and Service configuration](#part3)
    - [4. Service discovery and Health check](#part4)

Each step will have a given branch as a checkpoint.

## 🤘 Time to start


### 1.Decomposition <a id="part1"/>
As we have seen in the _**Brief overview about Microservices and Docker**_, microservices should be built accordingly with its own business logical capability.

Currently `search-api` service is requesting both **news** and **gifs** directly from external providers (instead of making use of the `news-api` and `giphy-api` service).

This does not seem to be right as **news** and **gifs** are completely different things and as so might have completely different requirements.

**Tasks to do:**

**1.1** Checkout branch start-workshop
```sh
git checkout start-workshop
```
**1.2** Download the docker images, build the project and spin up the containers 
```sh
docker-compose up --build
```
**1.3** On your browser access the `search-ui` in http://localhost:8080 and try search for _Trump_

**1.4** Change `search-api` service to start requesting through the `news-api` and the `giphy-api` services, instead of requesting directly the external services **newsapi.org** and **api.giphy.com** 


![](https://raw.githubusercontent.com/pipedrive/workshop-sinfo/master/.png/workshop-sinfo-1.png "img1")



```js
// on src/index.ts replace the router Get /search by the following code

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
	news: newsResponse ? newsResponse.searchResults : [],
	gifs: gifsResponse ? gifsResponse.searchResults : []
};
```

**1.5** On the `search-ui` try to search for anything else. What are the results?

**1.6** Stop the docker containers (on the console with the docker logs attach just hit `ctrl + c` or open a new terminal in the workshop-sinfo dir and run `docker-compose down`)

**1.7** Add the following configuration to the **docker-compose.yml**
```yaml
giphy-api:
  build: giphy-api/.
  ports:
    - 4000:4000
    - 5574:5574 #node debugger
  volumes:
    - ./giphy-api:/app/

 news-api:
   build: news-api/.
   ports:
     - 3500:3500
     - 5474:5474 #node debugger
   volumes:
    - ./news-api:/app/
```

**1.8** Rebuild the project
```sh
docker-compose up --build
```
**1.9** On the search-ui try search for anything again. What are the results now?

**1.10** Scale the `news-api` service to 2 instances and rebuild the project. Edit **docker-compose.yml** and add the following
    
```yaml
deploy:
    replicas: 2
```


### 2.API Gateway <a id="part2"/>
In a microservices architecture, ideally there should a be a single point of entry in order to access the back end services.

As so, we are going to change our current services so that the `search-api` service becomes our single point of entry -> ***news*** and ***gifs*** will be searched via `search-api`.

**Tasks to do:**

**2.1** If you didn't finish the **Decomposition** checkout branch api-gateway and rebuild the project.
```sh
git checkout api-gateway
docker-compose up --build
```

**2.2** Access directly the `news-api` and `giphy-api` services and check that each one returns a valid response.
* http://localhost:3500/news?search=<YOUR_SEARCH>
* http://localhost:4000/gifs?search=<YOUR_SEARCH>

**2.3** Edit `docker-compose.yml` and remove the port mapping for `news-api` and `giphy-api`.

![](https://raw.githubusercontent.com/pipedrive/workshop-sinfo/master/.png/workshop-sinfo-2.png "img2")

```yaml
  giphy-api:
    build: giphy-api/.
    ports:
      - 5574:5574 #node debugger
    volumes:
      - ./giphy-api:/app/

  news-api:
    build: news-api/.
    ports:
      - 5474:5474 #node debugger
    volumes:
      - ./news-api:/app/
```
**2.4** Stop the containers and rebuild the services
```sh
docker-compose down
docker-compose up --build
```

**2.5** Confirm that the previous endpoints are no longer accessible but that the you can still request the results through `search-api` (`http://localhost:5000/search?q=<YOUR_SEARCH>`) or using the `search-ui` app



### 3.Fault tolerance <a id="part3"/>
When a microservice is failing, the overall application should be able to keep working (even if at a reduced capacity). Also redeploying a new version of a specific service should have zero impact on the overall application.

**Tasks to do:**

**3.1** If you didn't finish the **API Gateway** checkout branch fault-tolerance and rebuild the project.
```sh
git checkout fault-tolerance
docker-compose up --build
```

**3.2** Stop one of the microservices (for example the `giphy-api`service)
Stop `giphy-api` container:
```sh
docker stop $(docker ps -a -q --filter ancestor=workshop-sinfo_giphy-api)
```

![](https://raw.githubusercontent.com/pipedrive/workshop-sinfo/master/.png/workshop-sinfo-3.png "img3")

**3.3** Confirm that the overall service keeps working even if no gifs are returned.

**3.4** Restart `giphy-api` container:
```sh
docker start $(docker ps -a -q --filter ancestor=workshop-sinfo_giphy-api)
```

**3.5** Change `news-api` GET /news endpoint, currently retrieving news from the _The New York Times_ to return news from every source.

* Remove ```sources: 'the-new-york-times'``` from the request url.

```js
const url = `https://newsapi.org/v2/everything?${qs.stringify({ q: searchQuery })}`;
```


### 4.Service discovery and health check <a id="part4"/>
In a microservices architecture, the number of services is intended to grow up to a level where it is impossible to rely on manual configuration for services to communicate with each other.
Moreover services are constantly being scaled up and scaled down. This means that they will get available Network IPs and assigned on the fly.

The solution is **service discovery** and we are going to use Consul for that.

**Tasks to do:**

**4.1** If you didn't finish the **Fault tolerance** checkout branch service-discovery and rebuild the project.
```sh
git checkout service-discovery
docker-compose up --build
```

**4.2** For service discovery, add the ``consul`` and ``registrator`` services

![](https://raw.githubusercontent.com/pipedrive/workshop-sinfo/master/.png/workshop-sinfo-4.png "img4")

```yml
# "infrastructure" services
# --------------------------------------------------------------------------------------

# consul is the service that's responsbile for service discovery
consul:
  image: gliderlabs/consul-server
  ports:
    - 9500:8500
  command: -bootstrap
  environment:
    SERVICE_8400_IGNORE: 'true'
    SERVICE_8300_IGNORE: 'true'
    SERVICE_8301_IGNORE: 'true'
    SERVICE_8302_IGNORE: 'true'
    SERVICE_8500_IGNORE: 'true'
    SERVICE_8600_IGNORE: 'true'

# container watcher that registers services in consul
registrator:
  image: gliderlabs/registrator
  depends_on:
    - consul
  command: -internal consul://consul:8500
  volumes:
    - /var/run/docker.sock:/tmp/docker.sock
```

**4.3** Open Consul UI and confirm that all the services are registered there. 
* http://localhost:9500/ui/#/dc1/services/

**4.4** Add health check endpoint to all BE services (`news-api`, `giphy-api`, `search-api`), before any other route.
```js
// health-check route
router.get('/health-check', (ctx) => ctx.status = 200);
```

**4.5** Add health check configurations to each service **Dockerfile**
```Dockerfile
SERVICE_CHECK_HTTP=/health-check \
SERVICE_CHECK_INTERVAL=15s \
SERVICE_CHECK_TIMEOUT=2s  \
```

**4.6** Stop the docker containers and rebuild the project.
```sh
docker-compose down
docker-compose up --build
```

**4.7** Open Consul UI and check that all services are passing both on container health and service health check.
* http://localhost:9500/ui/#/dc1/services/search-api

**4.8** Change the `giphy-api` health check endpoint to reply with http code **503**. Wait a couple of seconds and check that the service is now failing on consul.
```js
ctx.status = 503;
```

🏁
---

💚 [Pipedrive](https://www.pipedrive.com) workshop

_Built by Pipedrive_