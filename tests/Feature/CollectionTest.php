<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\LazyCollection;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5], $collection->all());
    }


    // Membuat Colection menggunakan Foreach
    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }


    // Manipulasi data collection
    public function testManipulationCRUD()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        assertEquals(3, $result);
        assertEqualsCanonicalizing([1, 2], $collection->all());
    }



    // Collection Mapping
    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($value) {
            return $value * 2;
        });
        assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }



    // Collection Mapping
    public function testMapInto()
    {
        $collection = collect(["Ican"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("Ican")], $result->all());
    }



    // Collection Map Spread
    public function testMapSpread()
    {
        $collection = collect([
            ["Ican", "Muhirsan"],
            ["Abdul", "Ghopar"]
        ]);
        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . ' ' . $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Ican Muhirsan"),
            new Person("Abdul Ghopar"),
        ], $result->all());
    }



    // Collection Map Group
    public function testMapToGroup()
    {
        $collection = collect([
            [
                "name" => "Muhirsan",
                "departement" => "IT"
            ],
            [
                "name" => "Ican",
                "departement" => "IT"
            ],
            [
                "name" => "Budi",
                "departement" => "QA"
            ]
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["departement"] => $person["name"]
            ];
        });
        $this->assertEquals([
            "IT" => collect(["Muhirsan", "Ican"]),
            "QA" => collect(["Budi"])
        ], $result->all());
    }



    // Celection ZIP
    public function testZIP()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6])
        ], $collection3->all());
    }



    // Celection Concat
    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }



    // Celection Combine
    public function testCombine()
    {
        $collection1 = ["name", "country"];
        $collection2 = ["Muhirsan", "Indonesia"];
        $collection3 = collect($collection1)->combine($collection2);

        assertEquals([
            "name" => "Muhirsan",
            "country" => "Indonesia"
        ], $collection3->all());
    }



    // Celection Collapse
    public function testCollspse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);
        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }



    // Celection Flat Map
    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "Muhirsan",
                "hobbies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Ican",
                "hobbies" => ["Reading", "Writing"]
            ],
        ]);

        $result = $collection->flatMap(function ($item) {
            $hobbies = $item["hobbies"];
            return $hobbies;
        });

        $this->assertEqualsCanonicalizing(["Coding", "Gaming", "Reading", "Writing"], $result->all());
    }



    // Collection Join
    public function testJoin()
    {
        $collection = collect(["Ican", "Muhirsan", "Tono"]);

        $this->assertEquals("Ican-Muhirsan-Tono", $collection->join("-"));
        $this->assertEquals("Ican, Muhirsan and Tono", $collection->join(", ", " and "));
    }



    // Collection Filtering
    public function testFiltering()
    {
        $collection = collect([
            "Ican" => 100,
            "Muhirsan" => 80,
            "Tono" => 90
        ]);

        $result = $collection->filter(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals(["Ican" => 100, "Tono" => 90], $result->all());
    }



    // Collection Filtering index
    public function testFilteringIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });
        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());
    }



    // Collection Operations
    public function testPartition()
    {
        $collection = collect([
            "Ican" => 100,
            "Muhirsan" => 80,
            "Tono" => 90
        ]);

        [$result1, $result2] = $collection->partition(function ($item, $key) {
            return $item >= 90;
        });

        $this->assertEquals(["Ican" => 100, "Tono" => 90], $result1->all());
        $this->assertEquals(["Muhirsan" => 80], $result2->all());
    }



    // Collection Testing
    public function testTesting()
    {
        $collection = collect(["Ican", "Muhirsan", "Tono"]);
        $this->assertTrue($collection->contains("Ican"));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == "Muhirsan";
        }));
    }



    // Collection Grouping
    public function testGrouping()
    {
        $collection = collect([
            [
                "name" => "Muhirsan",
                "departement" => "IT"
            ],
            [
                "name" => "Ican",
                "departement" => "IT"
            ],
            [
                "name" => "Budi",
                "departement" => "QA"
            ]
        ]);

        $result = $collection->groupBy("departement");
        assertEquals([
            "IT" => collect([
                ["name" => "Muhirsan", "departement" => "IT"],
                ["name" => "Ican", "departement" => "IT"]
            ]),
            "QA" => collect([
                ["name" => "Budi", "departement" => "QA"]
            ])
        ], $result->all());
    }



    // Collection Slice
    public function testSlice()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->slice(3);
        $this->assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->slice(offset: 3, length: 2);
        $this->assertEqualsCanonicalizing([4, 5], $result->all());
    }



    // Collection Take
    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->take(limit: 3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all());


        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });

        $this->assertEqualsCanonicalizing([1, 2], $result->all());
        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });

        $this->assertEqualsCanonicalizing([1, 2], $result->all());
    }



    // Collection Skip
    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->skip(count: 3);
        $this->assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipUntil(function ($value, $key) {
            return $value == 3;
        });
        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());


        $result = $collection->skipWhile(function ($value, $key) {
            return $value < 3;
        });
        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());
    }



    // Collection Chunked
    public function testChunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->chunk(3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4, 5, 6], $result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7, 8, 9], $result->all()[2]->all());
    }



    // Collection Retrive First
    public function testRetriveFirst()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->first();
        $this->assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });

        $this->assertEquals(6, $result);
    }



    // Collection Retrive Last
    public function testRetriveLast()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->last();
        $this->assertEquals(9, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 5;
        });

        $this->assertEquals(4, $result);
    }



    // Collection Retrive Random
    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->random();
        $this->assertTrue(in_array($result, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
    }



    // Collection Checking Existence
    public function testCheckingExitance()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertTrue($collection->isNotEmpty());
        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->contains(8));
        $this->assertFalse($collection->contains(10));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == 8;
        }));
    }



    // Collection Ordering
    public function testOrdering()
    {
        $collection = collect([1, 3, 2, 4, 5, 6, 8, 7, 9]);
        $result = $collection->sort();
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->sortDesc();
        $this->assertEqualsCanonicalizing([9, 8, 7, 6, 5, 4, 3, 2, 1], $result->all());
    }



    // Collection Agregate
    public function testAggregates()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->sum();
        $this->assertEquals(45, $result);

        $result = $collection->avg();
        $this->assertEquals(5, $result);

        $result = $collection->min();
        $this->assertEquals(1, $result);

        $result = $collection->max();
        $this->assertEquals(9, $result);
    }



    // Collection Reduce
    public function testReduce()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertEquals(45, $result);
    }



    // lazy Collection
    public function testLazyCollection()
    {
        $collection = LazyCollection::make(function () {
            $value = 0;
            while (true) {
                yield $value;
                $value++;
            }
        });

        $result = $collection->take(10);
        $this->assertEqualsCanonicalizing([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }
}
