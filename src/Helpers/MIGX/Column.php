<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/26/18
 * Time: 11:22 AM
 */

namespace LCI\Blend\Helpers\MIGX;


class Column
{
    protected $header = 'Please Set a Header';
    protected $width = 160;
    protected $sortable = true;

    protected $dataIndex = '';
    protected $renderer = 'this.renderImage';


    /**
     *
     * header 	the caption of the column
    sortable 	if the columns is sortable by clicking the header
    dataIndex 	the field, you want to render into this column
    renderer 	you can use a renderer for each column. For example the included function "this.renderImage". This will render an image-preview in the grid-cell, if you are using an image-TV for this field.
     * "editor": "this.textEditor"
     *
     */
}