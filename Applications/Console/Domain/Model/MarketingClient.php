<?php
namespace Applications\Console\Domain\Model;


use Core\Sql\Model\Model;

class MarketingClient extends Model
{
    protected $__table = 'marketing_clients';
    protected $__fields = [
        'id' => 'id',
        'gaId' => 'google_analytics_id',
        'ymId' => 'yandex_metrika_id',
        'registration' => 'registration_date'
    ];
    protected $__pKeys = ['id'];

    protected $id;
    protected $gaId;
    protected $ymId;
    protected $registration;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getGaId()
    {
        return $this->gaId;
    }

    /**
     * @return mixed
     */
    public function getYmId()
    {
        return $this->ymId;
    }

    /**
     * @return mixed
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @param $registration
     * @return $this
     */
    public function changeRegistration($registration)
    {
        $this->registration = (new \DateTime($registration))->format('Y-m-d H:i:s');
        return $this;
    }
}