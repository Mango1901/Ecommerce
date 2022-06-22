<?php
/**
 * Created by PhpStorm.
 * User: ducquach
 * Date: 3/9/21
 * Time: 1:34 PM
 */
namespace Magenest\ElasticsearchVi\Plugin;

use Magenest\ElasticsearchVi\Model\AccentAttribute;

class AddNonAccentFieldToQuery
{
    /**
     * @var AccentAttribute
     */
    private $accentAttribute;

    public function __construct(
        AccentAttribute $accentAttribute
    ) {
        $this->accentAttribute = $accentAttribute;
    }

    public function afterBuild($subject, $selectQueries)
    {
        if (!empty($selectQueries['bool']['should'])) {
            foreach ($selectQueries['bool']['should'] as $query) {
                if (!empty($query['match'])) {
                    $attributeName = array_key_first($query['match']);
                    if ($this->accentAttribute->isAttributeFilterableWithAccent($attributeName)) {
                        $match = reset($query['match']);
                        $selectQueries['bool']['should'][] = [
                            'match' => [
                                $attributeName.'.'.AccentAttribute::NON_ACCENT_KEY => $match
                            ]
                        ];
                    }
                }
            }
        }
        return $selectQueries;
    }
}