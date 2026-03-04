<?php

namespace WPDeveloper\BetterDocsPro\REST;
use WPDeveloper\BetterDocs\Core\BaseAPI;

class PopularKeywords extends BaseAPI {
    public function register() {
        $this->get( '/popular_search_keyword', [$this, 'popular_search_keyword'] );
    }

    public function popular_search_keyword( $object ) {
        return betterdocs_pro()->query->popular_search_keyword();
    }
}
