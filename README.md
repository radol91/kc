## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Authentication
I skipped the authentication part in the endpoint I provided. I would use JWT from `lexik/jwt-authentication-bundle`, but I ran out of time.

## Running tests
1. Start container
2. Run `docker compose exec php vendor/bin/phpunit`


## Running example from assignment
1. Start container
2. Navigate to `{PROJECT_DIR}/docs/api/quotes.http` and run the request


## Missing business logic in the assignment
I couldn't find an answer in the assignment about what should happen when more than two topics are matched by a provider. 
I know that in the sample JSON, each provider has only two topics, but I assumed the JSON file could change. 
Therefore, for cases where more than two topics are matched, I throw a NotImplemented exception (501).

## Mistake in quotes in the assignment
For sample JSON file:
```json
{
  "provider_topics": {
    "provider_a": "math+science",
    "provider_b": "reading+science",
    "provider_c": "history+math"
  }
}
```

and POST payload: 
```json
{
    "topics": {
        "reading": 20,
        "math": 50,
        "science": 30,
        "history": 15,
        "art": 10
    }
}
```

Provider C matched the `math` topic, which is "the highest requested topic", so the quote ratio for it is 0.2. 
In example for Provider C the expected value is 12.5, but it should probably be: 50 * 0.2 = 10. 

Provider B matched the `math` and `reading:` topics, which makes the application use the quote ratio for "2 topics match" so it is 0.1.
In example for Provider B the expected value is 5, and it is correct but it results from calculation: (20 + 30) * 0.1 = 5. 
