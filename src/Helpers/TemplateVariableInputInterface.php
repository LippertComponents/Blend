<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/26/18
 * Time: 11:16 AM
 */

namespace LCI\Blend\Helpers;


interface TemplateVariableInputInterface
{
    /**
     * TemplateVariableInputInterface constructor.
     * @param string $type
     */
    public function __construct(string $type);

    /**
     * @return array
     */
    public function getInputProperties(): array ;
}