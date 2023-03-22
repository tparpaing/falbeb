<?php

namespace Framework\Renderer;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;

class PaginationRenderer implements ViewInterface
{
    public function render(PagerfantaInterface $pagerfanta, callable $routeGenerator, array $options = []): string
    {
        $router = $options['router'];
        $nbPages = $pagerfanta->getNbPages();

        if ($nbPages === 1) {
            return '';
        }

        $currentPage = $pagerfanta->getCurrentPage();
        $previous = $currentPage == 1 ? null : $pagerfanta->getPreviousPage();
        $next = $currentPage == $nbPages ? null : $pagerfanta->getNextPage();

        $result = '';
        if ($previous === null) {
            $result .= '<li class="uk-disabled"><a><span uk-pagination-previous></span></a></li>';
        } else {
            $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $previous]) . '"><span uk-pagination-previous></span></a></li>';
        }

        if ($nbPages <= 11) {
            for ($i = 1; $i <= $nbPages; ++$i) {
                if ($i == $currentPage) {
                    $result .= '<li class="uk-active"><span>' . $i . '</span></li>';
                } else {
                    $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $i]) . '">' . $i . '</a></li>';
                }
            }
        } else {
            if ($currentPage <= 5) {
                for ($i = 1; $i <= $currentPage + 2; ++$i) {
                    if ($i == $currentPage) {
                        $result .= '<li class="uk-active"><span>' . $i . '</span></li>';
                    } else {
                        $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $i]) . '">' . $i . '</a></li>';
                    }
                }
                $result .= '<li class="uk-disabled"><span>…</span></li>';
                for ($i = ($nbPages - (5 - (\max(0, $currentPage - 3)))) + 1; $i <= $nbPages; ++$i) {
                    $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $i]) . '">' . $i . '</a></li>';
                }
            } else if ($currentPage >= $nbPages - 5) {
                for ($i = 1; $i <= (5 - (\max(0, ($nbPages - ($currentPage + 2))))); ++$i) {
                    $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $i]) . '">' . $i . '</a></li>';
                }
                $result .= '<li class="uk-disabled"><span>…</span></li>';
                for ($i = $currentPage - 2; $i <= $nbPages; ++$i) {
                    if ($i == $currentPage) {
                        $result .= '<li class="uk-active"><span>' . $i . '</span></li>';
                    } else {
                        $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $i]) . '">' . $i . '</a></li>';
                    }
                }
            } else {
                $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => 1]) . '">1</a></li>
                <li><a href="' . $router->generateUri('blog.index.page', ['page' => 2]) . '">2</a></li>
                <li><a href="' . $router->generateUri('blog.index.page', ['page' => 3]) . '">3</a></li>';
                if ($currentPage >= 8) {
                    $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => 4]) . '">4</a></li>';
                }
                $result .= '<li class="uk-disabled"><span>…</span></li>
                <li><a href="' . $router->generateUri('blog.index.page', ['page' => $currentPage - 1]) . '">' . $currentPage - 1 . '</a></li>
                <li class="uk-active"><span>' . $currentPage . '</span></li>
                <li><a href="' . $router->generateUri('blog.index.page', ['page' => $currentPage + 1]) . '">' . $currentPage + 1 . '</a></li>
                <li class="uk-disabled"><span>…</span></li>';
                if ($currentPage <= $nbPages - 7) {
                    $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $nbPages - 3]) . '">' . $nbPages - 3 . '</a></li>';
                }
                $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $nbPages - 2]) . '">' . $nbPages - 2 . '</a></li>
                <li><a href="' . $router->generateUri('blog.index.page', ['page' => $nbPages - 1]) . '">' . $nbPages - 1 . '</a></li>
                <li><a href="' . $router->generateUri('blog.index.page', ['page' => $nbPages]) . '">' . $nbPages . '</a></li>';
            }
        }

        if ($next === null) {
            $result .= '<li class="uk-disabled"><a><span uk-pagination-next></span></a></li>';
        } else {
            $result .= '<li><a href="' . $router->generateUri('blog.index.page', ['page' => $next]) . '"><span uk-pagination-next></span></a></li>';
        }

        return '<ul class="uk-pagination uk-flex-center" uk-margin>' . $result . '</ul>';
    }

    public function getName(): string
    {
        return 'pagination_renderer';
    }
}
