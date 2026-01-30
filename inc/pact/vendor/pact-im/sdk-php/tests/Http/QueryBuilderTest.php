<?php

namespace Pact\Tests\Http;

use Pact\Http\QueryBuilder;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    private $queryBuilder;
    
    protected function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder(); 
    }

    protected function tearDown(): void
    {
        unset($this->queryBuilder);
    }

    /**
     * @dataProvider dataprovider_query_builder
     */
    public function test_query_builder(array $queryData, string $expectedQuery): void
    {
        $queryString = $this->queryBuilder->build($queryData);

        $this->assertSame($expectedQuery, $queryString);
    }

    public function dataprovider_query_builder()
    {
        return [
            'Common query' => [
                [
                    'name' => 'Dan',
                    'token' => 'super_secret_token',
                ],
                'name=Dan&token=super_secret_token'
            ],
            'Query with array' => [
                [
                    'name' => 'Dan',
                    'todo' => [
                        'try',
                        'solve',
                        'learn',
                        'repeat',
                    ]
                ],
                'name=Dan&todo%5B%5D=try&todo%5B%5D=solve&todo%5B%5D=learn&todo%5B%5D=repeat',
            ],
            'Query with multidimentional array' => [
                [
                    'name' => 'Dan',
                    'contacts' => [
                        'phone' => [
                            '+79220000000',
                            '+75850000000',
                        ],
                        'email' => [
                            'dan@example.com'
                        ],
                    ],
                ],
                'name=Dan&contacts%5Bphone%5D%5B%5D=%2B79220000000&contacts%5Bphone%5D%5B%5D=%2B75850000000&contacts%5Bemail%5D%5B%5D=dan%40example.com'
            ]
        ];
    }

    public function test_query_builder_will_strip_number_keys_as_side_effect()
    {
        $queryData = [
            'reporter' => 'Dan',
            'reproduce' => [
                21554 => 'keys will',
                0 => 'be stripped',
            ],
        ];

        $expectedQuery = 'reporter=Dan&reproduce%5B%5D=keys+will&reproduce%5B%5D=be+stripped';

        $queryString = $this->queryBuilder->build($queryData);

        $this->assertSame($expectedQuery, $queryString);
    }
}
