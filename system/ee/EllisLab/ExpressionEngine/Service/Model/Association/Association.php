<?php

namespace EllisLab\ExpressionEngine\Service\Model\Association;

use EllisLab\ExpressionEngine\Service\Model\Collection;
use EllisLab\ExpressionEngine\Service\Model\Model;
use EllisLab\ExpressionEngine\Service\Model\Relation\Relation;

class Association {

    private $model;
    private $relation;

//    private $tracker;
    private $loaded = FALSE;
    private $inverse_name;

    protected $related;

    public function __construct(Model $model, Relation $relation)
    {
        $this->model = $model;
        $this->relation = $relation;

        $this->bootAssociation();
    }

    public function fill($related, $_skip_inverse = FALSE)
    {
        $this->related = $related;

        if ( ! $_skip_inverse)
        {
            $related = $this->toModelArray($related);

            foreach ($related as $to)
            {
                $this->relation->fillLinkIds($this->model, $to);
                $this->getInverse($to)->fill($this->model, TRUE);
            }
        }
    }

    public function set($item)
    {
        $this->remove();
        $items = $this->toModelArray($item);

        foreach ($items as $model)
        {
            $inverse = $this->getInverse($model);

            if ($inverse instanceOf ToOne)
            {
                $inverse->remove();
            }

            $this->addToRelated($model);
        }
    }

    public function getInverseName()
    {
        if ( ! isset($this->inverse_name))
        {
            $inverse = $this->relation->getInverse();

            if ( ! isset($inverse))
            {
                throw new \Exception('Cannot find inverse of the relationship '.$this->relation->getName().' in '.get_class($this->model));
            }

            $this->inverse_name = $inverse->getName();
        }

        return $this->inverse_name;
    }

    public function getInverse(Model $model)
    {
        $inverse_name = $this->getInverseName();
        return $model->getAssociation($inverse_name);
    }

    public function get()
    {
        if ( ! $this->isLoaded())
        {
            $this->reload();
        }

        // todo lazy load
        return $this->related;
    }

    public function add($item)
    {
        $items = $this->toModelArray($item);

        foreach ($items as $model)
        {
            $this->addToRelated($model);
        }
    }

    public function remove($items = NULL)
    {
        $items = $items ?: $this->related;
        $items = $this->toModelArray($items);

        foreach ($items as $model)
        {
            $this->removeFromRelated($model);
        }
    }

    public function idHasChanged()
    {
        $new_id = $this->model->getId();
        $items = $this->toModelArray($this->related);

        foreach ($items as $to)
        {
            $this->relation->linkIds($this->model, $to);
        }
    }

    /**
     * Save any unsaved relations and then the related models.
     */
    public function save()
    {
        /*
        foreach ($this->tracker->getRemoved() as $model)
        {
            $this->dropRelationship($this->source, $model);
        }

        foreach ($this->tracker->getAdded() as $model)
        {
            $this->insertRelationship($this->source, $model);
        }

        $this->tracker->reset();
*/
        if ($this->relation->canSaveAcross())
        {
            if (isset($this->related))
            {
                $this->related->save();
            }
        }
    }

    public function markAsLoaded()
    {
        $this->loaded = TRUE;
    }

    public function isLoaded()
    {
        return $this->loaded;
    }

	/**
	 *
	 */
	public function reload()
	{
		$query = $this->frontend->get($this->relation->getTargetModel());
		$query->setLazyConstraint($this->relation, $this->model);

		$result = $query->all();
		$this->fill($result);

		$this->markAsLoaded();
	}

    public function setFrontend($frontend)
    {
        $this->frontend = $frontend;
    }

    protected function addToRelated(Model $model)
    {
        $this->ensureExists($model);
        $this->ensureInverseExists($model);
    }

    protected function removeFromRelated(Model $model)
    {
        $this->ensureDoesNotExist($model);
        $this->ensureInverseDoesNotExist($model);
    }

    protected function ensureExists($model)
    {
    //    $this->tracker->add($model);
        $this->relation->linkIds($this->model, $model);
    }


    protected function ensureDoesNotExist($model)
    {
    //    $this->tracker->remove($model);
        $this->relation->unlinkIds($this->model, $model);
    }

    protected function ensureInverseExists($model)
    {
        $assoc = $this->getInverse($model);
        $assoc->ensureExists($this->model);
    }

    protected function ensureInverseDoesNotExist($model)
    {
        $assoc = $this->getInverse($model);
        $assoc->ensureDoesNotExist($this->model);
    }

    protected function toModelArray($item)
    {
        if (is_null($item))
        {
            return array();
        }

        if (is_array($item))
        {
            return $item;
        }

        if ($item instanceOf Model)
        {
            return array($item);
        }

        if ($item instanceOf Collection)
        {
            return $item->asArray();
        }

        throw new \InvalidArgumentException('Must be a model, collection, or array of models');
    }

    /**
     *
     */
    protected function bootAssociation()
    {
    //    $this->tracker = new Tracker\Staged();

        $that = $this;
        $this->model->on('setId', function() use ($that)
        {
            $that->idHasChanged();
        });
    }
}
