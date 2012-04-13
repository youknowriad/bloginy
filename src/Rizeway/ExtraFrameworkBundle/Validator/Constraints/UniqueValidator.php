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

namespace Rizeway\ExtraFrameworkBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use DOctrine\ORM\Proxy\Proxy;

/**
 * UniqueValidator
 */
class UniqueValidator extends ConstraintValidator
{
	protected $entityManager;

	public function __construct(EntityManager $entityManager = null)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param object Entity
	 * @param Constraint $constraint
	 * @return bool
	 */
	public function isValid($value, Constraint $constraint)
	{
		$class = get_class($value);
	    $classMetadata = $this->entityManager->getClassMetadata($class);
		$property = $constraint->property;

		if (!isset($classMetadata->fieldMappings[$property])) {
			throw new \LogicException('Mapping for \'' . $property . '\' doesn\'t exist for ' . $class);
		}

        $method = sprintf("get%s", \ucfirst($property));
		$propertyValue = $value->$method();

		if (null === ($entity = $this->entityManager->getRepository($class)->findOneBy(array($property => $propertyValue)))) {
		  return true;
		}

    if ($entity->getId() === $value->getId()) {
      return true;
    }
        
		$this->setMessage($constraint->message, array(
            'property' => $constraint->property,
        ));

        return false;
	}
}