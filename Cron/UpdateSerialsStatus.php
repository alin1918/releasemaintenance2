<?php

namespace SalesIgniter\Maintenance\Cron;

Use League\Period\Period;

class UpdateSerialsStatus
{

    private $SerialFactory;

    private $TicketFactory;

    private $date;

    private $datetime;

    protected $_localeResolver;

    public function __construct(
        \SalesIgniter\Rental\Model\SerialNumberDetailsFactory $SerialFactory,
        \SalesIgniter\Maintenance\Model\TicketFactory $TicketFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->_localeResolver = $localeResolver;
        $this->datetime = $datetime;
        $this->date = $date;
        $this->SerialFactory = $SerialFactory;
        $this->TicketFactory = $TicketFactory;
    }

    public function execute()
    {
        $timezone = new \DateTimeZone($this->date->getConfigTimezone());
        $dateNow = $this->date->date();
        $tickets = $this->TicketFactory->create()->getCollection();
        foreach ($tickets as $ticket) {
            // if ticket is between start and end dates mark serials in maintenance
            $ticketStart = new \DateTime($ticket->getStartDate(), $timezone);
            $ticketEnd = new \DateTime($ticket->getEndDate(), $timezone);
//            $periodTicket = new Period($ticketStart, $ticketEnd);
//            $nowStart = $dateNow->sub(new \DateInterval('PT1H'));
//            $nowEnd = $dateNow->add(new \DateInterval('PT2H'));
//            $periodNow = new Period($nowStart, $nowEnd);
            if ($dateNow >= $ticketStart && $dateNow <= $ticketEnd) {
                $serialsArray = $ticket->getSerialsArray();
                if ($serialsArray) {
                    $this->updateSerialStatus($serialsArray, $ticket, 'maintenance', true);
                }
            }
            $test = '';
            // if ticket is in the past (now is after end date) mark serials as available
            if ($dateNow > $ticketEnd) {
                $serialsArray = $this->getSerialsArray($ticket);
                $this->updateSerialStatus($serialsArray, $ticket, 'available', false);

            }
        }
    }

    private function updateSerialStatus($serialsArray, $ticket, $status, $updateMaintenanceTicketId)
    {
        foreach ($serialsArray as $serialItem) {
            $serialid = $this->SerialFactory->create()->getResource()->loadByProductIdandSerialNumber($serialItem, $ticket->getProductId());
            if ($updateMaintenanceTicketId) {
                $ticketid = $ticket->getId();
            } else {
                $ticketid = '0';
            }
            $this->SerialFactory->create()->load($serialid)->setStatus($status)->setMaintenanceTicketId($ticketid)->save();
        }
    }

    private function getSerialsArray($ticket)
    {
        $serials = $ticket->getSerials();
        if ($serials != '' || $serials != null) {
            $serialsArray = explode(',', $serials);
        } else {
            $serialsArray = '';
        }
        return $serialsArray;
    }
}