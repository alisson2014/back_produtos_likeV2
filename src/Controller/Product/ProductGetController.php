<?php

declare(strict_types=1);

namespace Produtos\Action\Controller\Product;

use Produtos\Action\Domain\Model\Product;
use Produtos\Action\Infrastructure\Repository\ProductRepository;
use Produtos\Action\Service\Helper;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

final class ProductGetController implements RequestHandlerInterface
{

    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request?->getQueryParams();
        $id = $queryParams["id"] ?? null;

        if (is_null($id)) {
            return $this->listProducts();
        }

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return Helper::invalidRequest("Id inválido.");
        }

        return $this->findProduct($id);
    }

    /** @return ResponseInterface */
    private function listProducts(): ResponseInterface
    {
        $productList = array_map(function (Product $product): array {
            $category = $this->productRepository->findCategorie($product->idCategoria);

            return [
                "id" => $product->id,
                "nomeProduto" => $product->nomeProduto,
                "categoria" => $category->nomeCategoria,
                "valor" => $product->valor
            ];
        }, $this->productRepository->all());

        if (empty($productList)) {
            return Helper::nothingFound();
        }

        return Helper::showResponse($productList);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     */
    private function findProduct(int $id): ResponseInterface
    {
        $product = $this->productRepository->find($id);
        if (empty($product)) {
            return Helper::nothingFound();
        }
        $category = $this->productRepository->findCategorie($product->idCategoria);
        $allOfProduct = [
            ...get_object_vars($product),
            "nomeCategoria" => $category->nomeCategoria
        ];
        return Helper::showResponse($allOfProduct);
    }
}
