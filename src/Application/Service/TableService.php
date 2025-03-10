<?php

namespace App\Application\Service;

use App\Application\Port\TableUseCaseInterface;
use App\Domain\Model\Table;
use App\Domain\Repository\TableRepositoryInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;

use Symfony\Component\Translation\Exception\NotFoundResourceException;
use DateTime;

class TableService implements TableUseCaseInterface
{
    private TableRepositoryInterface $tableRepository;
    private RestaurantRepositoryInterface $restaurantRepository;

    public function __construct(
        TableRepositoryInterface $tableRepository,
        RestaurantRepositoryInterface $restaurantRepository
    ) {
        $this->tableRepository = $tableRepository;
        $this->restaurantRepository = $restaurantRepository;
    }

    public function addTable(int $restaurantId, int $capacity, string $tableNumber): Table
    {
        $restaurant = $this->restaurantRepository->findById($restaurantId);

        if (!$restaurant) {
            throw new NotFoundResourceException("Restaurant not found.");
        }

        $table = $this->tableRepository->createNew();
        $table->setRestaurant($restaurant);
        $table->setCapacity($capacity);
        $table->setTableNumber($tableNumber);

        $this->tableRepository->save($table);

        return $table;
    }

    public function isTableAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool
    {
        return $this->tableRepository->isTableAvailable($tableId, $startTime, $endTime);
    }
}