<?php

namespace App\Controllers\SandboxControllers;

use App\Entities\Quality;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DatabaseSandboxController extends AbstractSandboxController
{
    private \PDO $pdo;

    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->pdo = $this->getContainer()->get(\PDO::class);

//        $this->_devCreateQuality();
        $this->_devUpdateQuality01();
//        $this->_devUpdateQuality02();

        return $response;
    }

    private function _devCreateQuality(): void
    {
        $data = [
            //все поля обязательны
            'name' => 'Common',
            'alias' => 'common',    //уникальное
            'sort' => 500,
        ];

        //ключи в массиве уже проверены на наличие
        $quality = Quality::create(
            $data['name'],
            $data['alias'],
            $data['sort'],
        );
        dd($quality);

        $query = 'insert into qualities (name, alias, sort) values (:name, :alias, :sort)';

        $stmt = $this->pdo->prepare($query);

        //данные уже должны быть готовые для вставки в бд
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':sort', $data['sort']);

        $stmt->execute();
    }

    private function _devUpdateQuality01()
    {
        $data = [
            'id' => 1,
            'alias' => 'common',
//            'sort' => rand(),
        ];

        //todo: Доступ к записям по alias => id можно вынести в отдельную логику.
        //todo: Придумать алгоритм взаимодействия по ID и alias.
        $queryByID = 'update qualities set sort = :sort where id = :id';
//        $queryByAlias = 'update qualities set sort = :sort where alias = :alias';

        $stmt = $this->pdo->prepare($queryByID);

        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':sort', $data['sort']);

        $stmt->execute();

        dump($stmt->rowCount());
    }

    private function _devUpdateQuality02()
    {
        $data = [
            'id' => 1,
            'alias' => 'common',
            'sort' => rand(),
        ];

        $this->updateByID($data['id'], $data);
        $this->updateByAlias($data['alias'], $data);
    }

    private function updateByID($id, array $data): int
    {
        $queryByID = 'update qualities set sort = :sort where id = :id';

        return $this->updateByQuery($queryByID, $data);
    }

    private function updateByAlias(string $alias, array $data): int
    {
        $queryByAlias = 'update qualities set sort = :sort where alias = :alias';

        return $this->updateByAlias($queryByAlias, $data);
    }
    private function updateByQuery(string $query, array $data): int
    {
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':sort', $data['sort']);

        $stmt->execute();

        return $stmt->rowCount();
    }
}