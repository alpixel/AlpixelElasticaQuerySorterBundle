<?php

namespace Alpixel\Bundle\ElasticaQuerySorterBundle\Services;

use Elastica\Query;
use Elastica\Query\AbstractQuery;
use FOS\ElasticaBundle\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class used in order to sort and paginate an Abstract Query
 * obtained from an elastic search repository
 * Data is stored in session to remember user choice
 */
class ElasticaQuerySorter
{

    protected $session;
    protected $request;
    protected $sessionData;

    const MAX_PER_PAGE = 25;
    const NO_LIMIT = 99999;

    public function __construct(RequestStack $requestStack, Session $session)
    {
        $this->session = $session;
        $this->request = $requestStack->getCurrentRequest();

        if (isset($this->request) && isset($this->request->query)) {
            if ($this->request->query->has('clear_sort')) {
                $this->session->remove('elastica_query_sorter');
            }
        }

        //Initializing the data in session
        if (!$this->session->has('elastica_query_sorter')) {
            $this->sessionData = [];
        } else {
            $this->sessionData = $this->session->get('elastica_query_sorter');
        }
    }

    public function sort(Repository $repository, Query $query, $nbPerPage = null)
    {
        if ($nbPerPage === null) {
            $nbPerPage = self::MAX_PER_PAGE;
        }

        //Creating the main elastica query
        $query->setFields(['_id']);

        //Analysing the request and the session data to add sorting
        $this->addSort($query);

        //Creating the paginator with the given repository
        $paginator = $repository->findPaginated($query);
        //If this a new sortBy, then we reset the currentPage to 1
        $paginator->setCurrentPage($this->getCurrentPage());

        $paginator->setMaxPerPage($nbPerPage);

        return $paginator;
    }

    protected function getCurrentPage()
    {
        $page = 1;

        if (!empty($this->request) && $this->request->getRealMethod() === 'GET') {
            $page = $this->getPage();
        }

        return $page;
    }

    protected function getPage()
    {
        $nbPage = null;
        if ($this->request->query->has('page')) {
            $nbPage = $this->request->query->get('page');
        }

        if (empty($nbPage) || !is_numeric($nbPage)) {
            return 1;
        }

        return $nbPage;
    }


    protected function addSort(Query &$query)
    {
        $sortBy    = explode('-', $this->fetchData('sortBy'));
        $sortOrder = $this->fetchData('sortOrder');

        $sort = array();
        foreach ($sortBy as $element) {
            if (empty($element) === false && empty($sortOrder) === false) {
                $sort[$element] = array(
                    'order'     => strtolower($sortOrder),
                    'missing'   => '_last'
                );
            }
        }

        if (!empty($sort)) {
            $query->setSort($sort);
        }

        return $query;
    }

    public function fetchData($key)
    {
        $pageKey = (!empty($this->request)) ? $this->request->getPathInfo() : null;
        $query   = (!empty($this->request)) ? $this->request->query : null;

        if ($query === null) {
            return;
        }

        //Analyzing the session object to see if there is data in it
        //If data is given from Request, it will be override the session data
        if (array_key_exists($pageKey, $this->sessionData) && !$query->has($key)) {
            if (isset($this->sessionData[$pageKey][$key])) {
                return $this->sessionData[$pageKey][$key];
            }
        }

        if ($query->has('sortBy')) {
            $value = $query->get($key);
            $this->sessionData[$pageKey][$key] = $value;
            $this->storeSessionData();
            return $value;
        }

        return null;
    }

    public function storeSessionData()
    {
        $this->session->set('elastica_query_sorter', $this->sessionData);
    }

    /**
     * Gets the value of session.
     *
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Gets the value of request.
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the value of sessionData.
     *
     * @return mixed
     */
    public function getSessionData()
    {
        return $this->sessionData;
    }
}
