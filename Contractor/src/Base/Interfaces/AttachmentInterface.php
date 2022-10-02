<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 27/3/2021
 * Time: 2:25 م
 */

namespace NawaGrow\Contractor\Base\Interfaces;


interface AttachmentInterface
{
    /**
     * @param $attachment
     * @return mixed
     * @author WeSSaM
     */
    public function upload($attachment);

    /**
     * @return mixed
     * @author WeSSaM
     */
    public function getFullPath();
}
