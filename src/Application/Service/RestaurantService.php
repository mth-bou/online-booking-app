<?php

namespace App\Application\Service;

use Exception;
use App\Domain\Model\Table;
use App\Domain\Model\Restaurant;
use App\Domain\Repository\TableRepositoryInterface;
use App\Domain\Repository\ReviewRepositoryInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class RestaurantService
{
    private RestaurantRepositoryInterface $restaurantRepository;
    private TableRepositoryInterface $tableRepository;
    private ReviewRepositoryInterface $reviewRepository;

    public function __construct(
        RestaurantRepositoryInterface $restaurantRepository,
        TableRepositoryInterface $tableRepository,
        ReviewRepositoryInterface $reviewRepository
    ) {
        $this->restaurantRepository = $restaurantRepository;
        $this->tableRepository = $tableRepository;
        $this->reviewRepository = $reviewRepository;
    }

    public function addRestaurant(
        string $name,
        string $type,
        string $description,
        string $address,
        string $city,
        string $postalCode,
        string $phoneNumber
    ): Restaurant {
        if ($this->restaurantRepository->findByName($name)) {
            throw new Exception("A restaurant with this name already exists.");
        }

        $restaurant = new Restaurant();
        $restaurant->setName($name);
        $restaurant->setType($type);
        $restaurant->setDescription($description);
        $restaurant->setAddress($address);
        $restaurant->setCity($city);
        $restaurant->setPostalCode($postalCode);
        $restaurant->setPhoneNumber($phoneNumber);

        $this->restaurantRepository->save($restaurant);

        return $restaurant;
    }

    public function updateRestaurant(int $restaurantId, array $data): Restaurant
    {
        $restaurant = $this->restaurantRepository->findById($restaurantId);

        if (!$restaurant) {
            throw new NotFoundResourceException("Restaurant not found.");
        }

        if (isset($data["name"]) && $this->restaurantRepository->findByName($data["name"])) {
            throw new Exception("A restaurant with this name already exists.");
        }

        if (isset($data["name"])) $restaurant->setName($data["name"]);

        if (isset($data["type"])) $restaurant->setType($data["type"]);

        if (isset($data["description"])) $restaurant->setDescription($data["description"]);

        if (isset($data["address"])) $restaurant->setAddress($data["address"]);

        if (isset($data["city"])) $restaurant->setCity($data["city"]);

        if (isset($data["postalCode"])) $restaurant->setPostalCode($data["postalCode"]);

        if (isset($data["phoneNumber"])) $restaurant->setPhoneNumber($data["phoneNumber"]);

        $this->restaurantRepository->save($restaurant);

        return $restaurant;
    }

    public function deleteRestaurant(int $restaurantId): void
    {
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        if (!$restaurant) {
            throw new NotFoundResourceException("Restaurant not found.");
        }

        $this->restaurantRepository->delete($restaurant);
    }

    public function findRestaurantById(int $restaurantId): ?Restaurant
    {
        return $this->restaurantRepository->findById($restaurantId);
    }

    public function findRestaurantByName(string $name): ?Restaurant
    {
        return $this->restaurantRepository->findByName($name);
    }

    public function findRestaurantsByCity(string $city): array
    {
        return $this->restaurantRepository->findByCity($city);
    }

    public function findRestaurantsByMinimumCapacity(int $capacity): array
    {
        return $this->restaurantRepository->findByMinimumCapacity($capacity);
    }

    public function searchRestaurants(string $keyword): array
    {
        return $this->restaurantRepository->search($keyword);
    }

    public function getAllRestaurants(): array
    {
        return $this->restaurantRepository->findAll();
    }

    public function getRestaurantTables(int $restaurantId): array
    {
        return $this->tableRepository->findByRestaurant( $restaurantId );
    }

    public function addTableToRestaurant(int $restaurantId, int $capacity, string $tableNumber): Table
    {
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        if (!$restaurant) {
            throw new NotFoundResourceException("Restaurant not found.");
        }

        $table = new Table();
        $table->setRestaurant($restaurant);
        $table->setCapacity($capacity);
        $table->setTableNumber($tableNumber);

        $this->tableRepository->save($table);

        return $table;
    }

    public function removeTableFromRestaurant(int $tableId): void
    {
        $table = $this->tableRepository->findById($tableId);
        if (!$table) {
            throw new NotFoundResourceException("Table not found.");
        }

        $this->tableRepository->delete($table);
    }

    public function getRestaurantReviews(int $restaurantId): array
    {
        return $this->reviewRepository->findByRestaurant($restaurantId);
    }

    public function calculateAverageRating(int $restaurantId): ?float
    {
        return $this->reviewRepository->getAverageRating($restaurantId);
    }
}