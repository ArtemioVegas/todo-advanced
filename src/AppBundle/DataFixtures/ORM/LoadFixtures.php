<?php
namespace AppBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

class LoadFixtures extends AbstractLoader
{
    public function getFixtures()
    {
        return [
            __DIR__.'/fixtures.yml',
        ];
    }
}