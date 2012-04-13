<?php

/**
  *  Bloginy, Blog Aggregator
  *  Copyright (C) 2012  Riad Benguella - Rizeway
  *
  *  This program is free software: you can redistribute it and/or modify
  *
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rizeway\UserBundle\Lib\Utils;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Avatar
{

    const TYPE_UPLOADED = 'uploaded';
    const TYPE_BIG      = 'big';
    const TYPE_MEDIUM   = 'medium';
    const TYPE_SMALL    = 'small';

    /**
     * Use Gravatar or not
     * 
     * @var bool
     */
    protected $use_gravatar = true;

    /**
     * filename of the avatar
     * 
     * @var string
     */
    protected $path;

    public $file;

    public function getAbsolutePath($type = self::TYPE_UPLOADED)
    {
        return $this->getUploadRootDir().'/'. (\is_null($this->path) ? $this->getPath($type, $this->getDefaultAvatar()) : $this->getPath($type));
    }

    public function getWebPath($type = self::TYPE_UPLOADED)
    {
        return $this->getUploadDir().'/'. (\is_null($this->path) ? $this->getPath($type, $this->getDefaultAvatar()) : $this->getPath($type));
    }

    /**
     * Upload the file to the upload directory with the filename in param
     *
     * @param string $filename
     * @return void
     */
    public function update($filename)
    {
        if ($this->use_gravatar)
        {
            $this->path = null;
        }
        elseif (!\is_null($this->file))
        {
            $path = $filename.'.'.$this->file->guessExtension();
            if (\is_file($this->getUploadRootDir().'/'.$path))
            {
                \unlink($this->getUploadRootDir().'/'.$path);
            }
            $this->file->move($this->getUploadRootDir(), $path);
            $this->setPath($path);
            unset($this->file);

            try {
                $this->generateThumbnails(); 
            } catch (\Exception $e) {
                $this->setPath(null);
                $this->setUseGravatar(true);
            }
        }
        else
        {
            $this->path = $this->getDefaultAvatar();
        }
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getDefaultAvatar()
    {
        return 'bloginy_default_avatar.png';
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/avatars';
    }

    public function getUseGravatar()
    {
        return $this->use_gravatar;
    }

    public function setUseGravatar($use_gravatar)
    {
        $this->use_gravatar = $use_gravatar;
    }

    public function getPath($type = self::TYPE_UPLOADED, $path = null)
    {
        $path = (\is_null($path)) ? $this->path : $path;
        if ($type == self::TYPE_UPLOADED)
        {
            return $path;
        }
        else
        {
            $extension = \substr($path, \strrpos($path, '.') + 1);
            $path_without_extension = \substr($path, 0, \strrpos($path, '.'));

            return $path_without_extension.'_'.$type.'.'.$extension;
        }
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    protected function generateThumbnails()
    {
        $this->generateThumbnail(100, 100, self::TYPE_BIG);
        $this->generateThumbnail(50, 50, self::TYPE_MEDIUM);
        $this->generateThumbnail(20, 20, self::TYPE_SMALL);     
    }

    protected function generateThumbnail($width, $heigth, $type)
    {
        $imagine = new Imagine();
        $imagine->open($this->getAbsolutePath())
            ->thumbnail(new Box($width, $heigth), ImageInterface::THUMBNAIL_OUTBOUND)
            ->save($this->getAbsolutePath($type));
    }

    public function getWidthForType($type)
    {
        switch ($type)
        {
            case self::TYPE_SMALL:  return 20;
            case self::TYPE_MEDIUM: return 50;
            case self::TYPE_BIG:    return 100;
            default:                return 100;
        }
    }

}