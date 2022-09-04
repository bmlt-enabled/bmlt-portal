
help:  ## Print the help documentation
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: build
build:  ## Docker Build
	docker build -f docker/Dockerfile . -t bmlt-portal:latest

.PHONY: run
run:  ## Docker Run
	docker run -t -p 8888:8000 -v $(pwd):/var/www/html -w /var/www/html bmlt-portal:latest
