<?php
namespace GlobalSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{

    public function globalSearchAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $searchValue = $this->getSearchValue($request->getPost());
            if (is_null($searchValue)) {
                throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' Says: A search value must be supplied');
            }

            $config = $this->serviceLocator->get('config');
            $canSearch = $this->canSearch($config);
            if (!$canSearch) {
                throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' Says: No search config has been defined');
            }

            $searchObjects = $config['global_search']['search_objects'];
            $searchNamespace = $config['global_search']['search_namespace'];
            $searchResults = [];

            $user = User::getLoggedIn();
            foreach($searchObjects as $objectType => $details)
            {
                $values = $this->fetchSearchData($objectType, $searchNamespace, $searchValue, $details['fields']);
                $objectResult = $this->formatSearchResults($details, $values, $searchValue);
                $searchResults[$objectType] = $objectResult;
            }

        }

        $result = new JsonModel([
            'SearchResult' => $searchResults
        ]);

        return $result;
    }

    /**
     * return a formatted search term from POST data
     * @param array $data POST data
     * @return string
     */
    private function getSearchValue($data)
    {
        if (!isset($data->searchvalue) || is_null($data->searchvalue) || strlen($data->searchvalue) === 0)
            return  null;

        $searchValue = $data->searchvalue;
        $searchValue = str_replace(' ', '%', $searchValue);

        return $searchValue;
    }

    /**
     * determine whether a vaid search config exists
     * @param array $config
     * @return bool
     */
    private function canSearch(array $config = [])
    {
        if (isset($config['global_search']) && isset($config['global_search']['search_objects']) === true)
        {
            if (is_array($config['global_search']['search_objects']) && count($config['global_search']['search_objects'] == 0))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * query a given $objectType for a given search term
     * @param string $objectType object PHP name
     * @param string $searchValue
     * @param array $fields an array of field names to include in the search query
     * @return PropelObjectCollection
     */
    private function fetchSearchData($objectType, $searchNamespace, $searchValue, array $fields = [])
    {
        $object = (is_null($searchNamespace) ? '' : $searchNamespace . '\\') . $objectType;
        $mapObject =  '\Map\\' . $object . 'TableMap';
        $tableName = $mapObject::getTableMap()->getName();
        $queryObject = $object . 'Query';

        $values = $queryObject::create()->addSelfSelectColumns();

        $fieldCount = count($fields);
        $fieldIndex = 0;
        foreach($fields as $fieldKey => $fieldName)
        {
            $values->where($tableName . "." . $fieldName . " LIKE '%" . $searchValue . "%'");
            $fieldIndex++;
            if ($fieldIndex < $fieldCount)
            {
                $values->_or();
            }
        }
        $values->find();

        return $values;
    }

    /**
     * format search data
     * @param array $objectDetails
     * @param PropelObjectCollection|array $objectValues
     * @param string $searchValue
     * @return array
     */
    private function formatSearchResults($objectDetails, $objectValues, $searchValue)
    {
        $objectResult = [];
        $fields = $objectDetails['fields'];

        foreach($objectValues as $object)
        {
            $urlParams =
            $url =  $this->url()->fromRoute($objectDetails['route'], ['id' => $object->getPrimaryKey()]);
            $objectResult[$object->getPrimaryKey()] = [
                'id' => $object->getPrimaryKey(),
                'url' => $url,
                'icon' => $objectDetails['icon'],
                'displayObject' => $objectDetails['displayObject'],
                'displayFields' => implode(', ', $objectDetails['displayFields']),
            ];

            $resultString = '';
            $results = [];
            foreach($fields as $fieldKey => $fieldName)
            {
                $method = 'get' . $fieldName;
                $fieldValue = $object->$method();
                $objectResult[$object->getPrimaryKey()][$fieldName] = $fieldValue;
                $results[] = is_null($fieldValue) ? '<span class="badge alert-warning">Unknown</span>' : $fieldValue;

            }

            $resultString .= implode(', ', $results);

            $resultString = str_ireplace($searchValue, '<span class="gs-highlight">' . $searchValue . '</span>', $resultString);
            $objectResult[$object->getPrimaryKey()]['resultString'] = $resultString;
        }

        return $objectResult;
    }
}
