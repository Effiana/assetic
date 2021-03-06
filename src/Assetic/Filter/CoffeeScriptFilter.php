<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Process;
use Assetic\Util\FilesystemUtils;

/**
 * Compiles CoffeeScript into Javascript.
 *
 * @link http://coffeescript.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CoffeeScriptFilter extends BaseNodeFilter
{
    private $coffeeBin;
    private $nodeBin;

    // coffee options
    private $bare;
    private $noHeader;

    public function __construct($coffeeBin = '/usr/bin/coffee', $nodeBin = null)
    {
        $this->coffeeBin = $coffeeBin;
        $this->nodeBin = $nodeBin;
    }

    public function setBare($bare)
    {
        $this->bare = $bare;
    }

    public function setNoHeader($noHeader)
    {
        $this->noHeader = $noHeader;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = FilesystemUtils::createTemporaryFile('coffee');
        file_put_contents($input, $asset->getContent());

        $commandline =$this->nodeBin
            ? array($this->nodeBin, $this->coffeeBin)
            : array($this->coffeeBin);

        array_push($commandline, '-cp');

        if ($this->bare) {
            array_push($commandline, '--bare');
        }

        if ($this->noHeader) {
            array_push($commandline, '--no-header');
        }

        array_push($commandline, $input);
        $proc = Process::fromShellCommandline(implode(' ', $commandline));
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
