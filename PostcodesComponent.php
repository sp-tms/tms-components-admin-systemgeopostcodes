<?php

namespace Apps\Tms\Components\System\Geo\Postcodes;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class PostcodesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoPostcodes;

    public function initialize()
    {
        $this->geoPostcodes = $this->basepackages->geoPostcodes->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $statesArr = $this->basepackages->geoStates->geoStates;
        $countriesArr = $this->basepackages->geoCountries->geoCountries;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $postcode = $this->basepackages->geoPostcodes->getById($this->getData()['id']);

                $this->view->postcode = $postcode;
            }

            if (!$this->view->postcode) {
                return $this->throwIdNotFound();
            }

            $this->view->pick('postcodes/view');

            $this->view->countries = [$countriesArr[$postcode['country_id']]];
            $this->view->states = [$statesArr[$postcode['state_id']]];

            return;
        }

        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'view'      => 'system/geo/postcodes',
                ]
            ];

        if ($this->request->isPost()) {
            $countries = [];
            $states = [];

            if ($countriesArr) {
                foreach ($countriesArr as $countriesKey => $country) {
                    $countries[$country['id']] = $country['name'] . ' (' . $country['id'] . ')';
                }
            }

            if ($statesArr) {
                foreach ($statesArr as $statesKey => $state) {
                    $states[$state['id']] = $state['name'] . ' (' . $state['id'] . ')';
                }
            }

            $replaceColumns =
                [
                    'state_id'  =>
                        [
                            'html' => $states
                        ],
                    'country_id'  =>
                        [
                            'html' => $countries
                        ],
                ];
        } else {
            $replaceColumns = [];
        }

        $this->generateDTContent(
            $this->geoPostcodes,
            'system/geo/postcodes/view',
            null,
            ['code', 'name', 'state_id', 'country_id'],
            true,
            ['code', 'name', 'state_id', 'country_id'],
            $controlActions,
            ['code'=>'post code','state_id'=>'State','country_id'=>'country'],
            $replaceColumns,
            'code'
        );

        $this->view->pick('postcodes/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->geoPostcodes->addPostcode($this->postData());

        $this->addResponse(
            $this->geoPostcodes->packagesData->responseMessage,
            $this->geoPostcodes->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->geoPostcodes->updatePostcode($this->postData());

        $this->addResponse(
            $this->geoPostcodes->packagesData->responseMessage,
            $this->geoPostcodes->packagesData->responseCode
        );
    }

    public function searchPostcodeAction()
    {
        $this->requestIsPost();

        if ($this->postData()['search']) {
            $searchQuery = $this->postData()['search'];

            if (strlen($searchQuery) < 3) {
                return;
            }

            $this->basepackages->geoPostcodes->searchPostcodes($searchQuery);

            $this->addResponse(
                $this->basepackages->geoPostcodes->packagesData->responseMessage,
                $this->basepackages->geoPostcodes->packagesData->responseCode,
                $this->basepackages->geoPostcodes->packagesData->responseData ?? []
            );
        } else {
            $this->addResponse('Search Query Missing', 1);
        }
    }

    public function searchPostCodeNameAction()
    {
        $this->requestIsPost();

        if ($this->postData()['search']) {
            $searchQuery = $this->postData()['search'];

            if (strlen($searchQuery) < 3) {
                return;
            }

            $this->basepackages->geoPostcodes->searchPostCodes($searchQuery);

            $this->addResponse(
                $this->basepackages->geoPostcodes->packagesData->responseMessage,
                $this->basepackages->geoPostcodes->packagesData->responseCode,
                $this->basepackages->geoPostcodes->packagesData->responseData ?? []
            );
        } else {
            $this->addResponse('Search Query Missing', 1);
        }
    }
}
