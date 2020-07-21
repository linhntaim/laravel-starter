# Launch application with Docker

## Requirements

Register & Verify Docker account:

https://hub.docker.com/signup

Install Docker:

https://docs.docker.com/install

## Launch

First, duplicate these file, then rename them and change some configuration if needed:

```
database.sql.example            => database.sql
docker-compose.yml.example      => docker-compose.yml
``` 

If you want to change the ports, go to ports section:

```
ports:
  - "80:80" // Port for HTTP listening
  - "3306:3306" // Port for MySQL listening
```

Keep it the same if you want to access web server at `localhost:80` and MySQL database at `localhost:3306`.

Launch Docker:

- Windows

```cmd
cd SOURCE_DIR\.docker
.\docker-up.bat

# Notice: Database will be kept. If need to get new database:
.\docker-up.bat --db-refesh
```

- Linux

```bash
cd SOURCE_DIR/.docker

bash docker-up.sh

# Notice: Database will be kept. If need to get new database:
bash docker-up.sh --db-refesh
```

Finally, launch browser and access `http://localhost`. If it shows an Welcome Page then it's OK.

## Stop

If not using the Docker, please stop it:

- Windows

```cmd
cd SOURCE_DIR\.docker
.\docker-down.bat
```

- Linux

```bash
cd SOURCE_DIR/.docker
bash docker-down.sh
```

When launching browser and access `http://localhost`, it shows nothing or network error.

## Access bash

```
docker-compose exec php bash
```

- `php` is the name of docker container configured in `.yml` file.
