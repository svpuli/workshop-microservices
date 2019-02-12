#!/bin/sh

# installs node dependencies when the container starts
npm install

# then compiles typescript on the fly and starts the server
npx ts-node-dev --transpileOnly --inspect=0.0.0.0:5574 --respawn src/index.ts
