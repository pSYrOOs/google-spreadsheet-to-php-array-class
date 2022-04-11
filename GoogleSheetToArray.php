<?php

use GuzzleHttp\Client;

class GoogleSheetToArray
{
    // Google sheets API key
    private $api_key = 'YOUR_API_KEY';

    // Google sheets API url
    private $api_url = 'https://sheets.googleapis.com/v4/spreadsheets/';

    /**
     * @var object
     */
    protected $properties;

    /**
     * @var string
     */
    protected $sheet_id;

    /**
     * @var int
     */
    protected $columns;

    /**
     * @var string
     */
    protected $sheet_url;

    /**
     * @var int
     */
    protected $rows;

    /**
     * @var int
     */
    protected $frozen_rows;


    /**
     * API Request
     *
     * @param $uri
     * @return mixed
     */
    protected function api_request($uri)
    {
        $http = new Client;

        $response = $http->get($uri, ['query' => ['key' => $this->api_key], 'verify' => false]);

        return json_decode($response->getBody());
    }


    /**
     *  Get properties of sheet
     */
    protected function get_sheet_properties()
    {
        $this->get_id();

        $uri = $this->api_url . $this->sheet_id;

        $response = $this->api_request($uri);

        $this->properties = $response->sheets[0]->properties->gridProperties;
    }


    /**
     * Parse url of sheet link to get ID
     */
    protected function get_id()
    {
        $parsed = parse_url($this->sheet_url, PHP_URL_PATH);

        $this->sheet_id = ($parsed) ? explode('/', $parsed)[3] : '';
    }


    /**
     * Set properties rows, columns, frozen rows
     */
    protected function set_properties()
    {
        $this->get_sheet_properties();

        $this->frozen_rows = $this->properties->frozenRowCount;

        $this->columns = $this->properties->columnCount;

        $this->rows = $this->properties->rowCount;
    }


    /**
     * Set sheet range
     *
     * @return string
     */
    protected function set_range(): string
    {
        // This works for sheets with a maximum range from A to Z.
        // For a larger range, you just need to refine this function.
        $alphabet = range('A', 'Z');
        
        return 'A1:' . $alphabet[($this->columns < 26) ? $this->columns : 25] . $this->rows;
    }


    /**
     * Get sheet data
     *
     * @return mixed
     */
    protected function sheet_data()
    {
        $uri = $this->api_url . $this->sheet_id . '/values/' . $this->set_range();

        return $this->api_request($uri);
    }


    /**
     * Return the array of the spreadsheet content
     *
     * @param $url
     * @return array
     */
    public function get_sheet_data($url): array
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {

            $this->sheet_url = $url;

            $this->set_properties();

            $data = $this->sheet_data()->values;

            $titles = $data[$this->frozen_rows - 1];

            $data = array_slice($data, $this->frozen_rows);

            $sheet_data = [];

            foreach ($data as $row) {
                $sheet_data[] = array_combine($titles, $row);
            }

            return $sheet_data;
        }

        return [];
    }
}
