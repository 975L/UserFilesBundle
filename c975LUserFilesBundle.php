<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class c975LUserFilesBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
