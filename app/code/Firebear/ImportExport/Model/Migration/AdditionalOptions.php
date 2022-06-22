<?php

/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Migration;

class AdditionalOptions
{
    /**
     * @var string|null
     */
    protected $migrateFromDate;

    /**
     * @return string|null
     */
    public function getMigrateFromDate()
    {
        return $this->migrateFromDate;
    }

    /**
     * @param string|null $migratedFromDate
     */
    public function setMigrateFromDate($migratedFromDate)
    {
        $this->migrateFromDate = $migratedFromDate;
    }
}
