<?php

namespace App\Repositories\QSS;

use App\Models\Author;
use App\Clients\Qss\Client as QssClient;
use App\Transformers\Qss\AuthorTransformer as QssAuthorTransformer;
use App\Clients\Qss\Exceptions\RequestException as QssRequestException;
use App\Repositories\Contracts\AuthorRepository as AuthorRepositoryInterface;
use App\Clients\Qss\Exceptions\ModelNotFoundException as QssModelNotFoundException;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function __construct(protected QssClient $client) {}

    public function all(): array
    {
        try {
            $response = $this->client->getAuthors();
            $items = json_decode($response->getBody(), true)['items'];

            $authors = array_map(function ($item) {
                return Author::createFromTransformer(new QssAuthorTransformer($item));
            }, $items);

            return $authors;
        } catch(QssRequestException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function find($id): ?Author
    {
        try {
            $response = $this->client->getAuthor($id);
            $item = json_decode($response->getBody(), true);

            return Author::createFromTransformer(new QssAuthorTransformer($item));
        } catch(QssModelNotFoundException) {
            return null;
        } catch(QssRequestException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function delete(mixed $id): bool
    {
        try {
            $this->client->deleteAuthor($id);
            return true;
        } catch(QssModelNotFoundException) {
            return false;
        } catch(QssRequestException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }
}