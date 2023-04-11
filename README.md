## Deployment

```bash
  docker-compose up -d –build
```
```bash
  php bin/console make:migration
```
```bash
  php bin/console doctrine:migrations:migrate
```
```bash
  symfony console doctrine:fixtures:load
```