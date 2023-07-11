<?php

declare(strict_types=1);

namespace Produtos\Action\Controller\Product;

use Produtos\Action\Domain\Model\Product;
use Produtos\Action\Infrastructure\Repository\ProductRepository;
use Produtos\Action\Service\Show;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;

final class ProductPostController implements RequestHandlerInterface
{
    use Show;
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents());
        $produto = $body->nomeProduto;
        $valor = $body->valor;
        $idCategoria = $body->idCategoria;

        $product = new Product($produto, $valor, $idCategoria);
        $success = $this->productRepository->add($product);

        if (!$success) {
            return $this->showInternalError();
        }

        return $this->showStatus("Categoria cadastrada com sucesso", 201);
    }
}
