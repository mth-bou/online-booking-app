<?php

namespace App\Application\Service;

use App\Application\Port\RestaurantUseCaseInterface;
use App\Domain\Model\Restaurant;
use App\Domain\Model\Table;
use App\Domain\Repository\TableRepositoryInterface;
use App\Domain\Repository\ReviewRepositoryInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;

use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Exception;

class RestaurantService implements RestaurantUseCaseInterface
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

    public function createRestaurant(
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

        if (isset($data["name"])) {
            $existingRestaurant = $this->restaurantRepository->findByName($data["name"]);
            if ($existingRestaurant && $existingRestaurant->getId() !== $restaurantId) {
                throw new Exception("A restaurant with this name already exists.");
            }
            $restaurant->setName($data["name"]);
        }

        foreach (['type', 'description', 'address', 'city', 'postalCode', 'phoneNumber'] as $field) {
            if (isset($data[$field])) {
                $setter = 'set' . ucfirst($field);
                $restaurant->$setter($data[$field]);
            }
        }

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

    public function getRestaurantById(int $restaurantId): ?Restaurant
    {
        return $this->restaurantRepository->findById($restaurantId);
    }

    public function getRestaurantByName(string $name): ?Restaurant
    {
        return $this->restaurantRepository->findByName($name);
    }

    public function getRestaurantsByCity(string $city): array
    {
        return $this->restaurantRepository->findByCity($city);
    }

    public function getRestaurantsByMinimumCapacity(int $capacity): array
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
        return $this->reviewRepository->getAverageRating($restaurantId) ?? 0.0;
    }
}