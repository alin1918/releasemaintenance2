<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SalesIgniter\Maintenance\Model;

use Magento\Framework\Exception\LocalizedException as CoreException;

/**
 * SendFriend Log
 *
 * @method \Magento\SendFriend\Model\ResourceModel\SendFriend _getResource()
 * @method \Magento\SendFriend\Model\ResourceModel\SendFriend getResource()
 * @method int getIp()
 * @method \Magento\SendFriend\Model\SendFriend setIp(int $value)
 * @method int getTime()
 * @method \Magento\SendFriend\Model\SendFriend setTime(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EmailTicket extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Recipient Names
     *
     * @var array
     */
    protected $_names = [];

    /**
     * Recipient Emails
     *
     * @var array
     */
    protected $_emails = [];

    /**
     * Sender data array
     *
     * @var \Magento\Framework\DataObject|array
     */
    protected $_sender = [];

    /**
     * Product Instance
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Count of sent in last period
     *
     * @var int
     */
    protected $_sentCount;

    /**
     * Last values for Cookie
     *
     * @var string
     */
    protected $_lastCookieValue = [];

    /**
     * SendFriend data
     *
     * @var \Magento\SendFriend\Helper\Data
     */
    protected $_sendfriendData = null;

    /**
     * Catalog image
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_catalogImage = null;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    protected $scopeConfig;

    protected $maintainerFactory;

    private $productRepository;

    private $urlBuilder;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Catalog\Helper\Image $catalogImage
     * @param \Magento\SendFriend\Helper\Data $sendfriendData
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Magento\SendFriend\Helper\Data $sendfriendData,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \SalesIgniter\Maintenance\Model\MaintainerToAdminFactory $maintainerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->productRepository = $productRepository;
        $this->maintainerFactory = $maintainerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_catalogImage = $catalogImage;
        $this->_sendfriendData = $sendfriendData;
        $this->_escaper = $escaper;
        $this->remoteAddress = $remoteAddress;
        $this->cookieManager = $cookieManager;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     * @throws CoreException
     */
    public function send($data)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $this->inlineTranslation->suspend();

        $maintainers = $this->maintainerFactory->create()->getCollection()->joinAdminTable();
        $maintainerAdmin = $maintainers->getItemById($data['maintainer_id']);

        $emailTo = $maintainerAdmin->getEmail();
        $nameTo = $maintainerAdmin->getFirstname() . ' ' . $maintainerAdmin->getLastname();

        $product = $this->productRepository->getById((int)$data['product_id'])->getName();
        $summary = nl2br(htmlspecialchars($data['summary']));
        $description = nl2br(htmlspecialchars($data['description']));
        $comments = nl2br(htmlspecialchars($data['comments']));
        $params = ['ticket_id' => $data['ticket_id']];
        $view = $this->urlBuilder->getUrl('adminhtml/salesigniter_maintenance/edit',$params);

//        $message = nl2br(htmlspecialchars($this->getSender()->getMessage()));
        $sender = [
            'name' => $this->_escaper->escapeHtml($this->scopeConfig->getValue( 'trans_email/ident_general/name', $storeScope)),
            'email' => $this->_escaper->escapeHtml($this->scopeConfig->getValue( 'trans_email/ident_general/email', $storeScope)),
        ];

            $this->_transportBuilder->setTemplateIdentifier(
                $this->scopeConfig->getValue('salesigniter_rental/emails/maintenance', $storeScope)
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )->setFrom(
                $sender
            )->setTemplateVars(
                [
                    'product' => $product,
                    'summary' => $summary,
                    'description' => $description,
                    'comments' => $comments,
                    'view' => $view
                ]
            )->addTo(
                $emailTo,
                $nameTo
            );
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

}
