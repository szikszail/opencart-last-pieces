<?php 
class ControllerProductLastpieces extends Controller {
	public function index() { 
		$this->language->load('product/lastpieces');
		$this->load->model('catalog/productextended');
		$this->load->model('tool/image');

        $sort = $this->request->_get('sort', 'p.sort_order');
        $order = $this->request->_get('order', 'ASC');
        $page = $this->request->_get('page', 1);
        $limit = $this->request->_get('limit', $this->config->get('config_catalog_limit'));

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('catalog/view/javascript/jquery/jquery.total-storage.min.js');

		$GLOBALS['breadcrumbs'] = array();
		$GLOBALS['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);

        $url = $this->request->_urls(array(
            'page', 'sort', 'order', 'limit'
        ));
		$GLOBALS['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('product/lastpieces', $url),
			'separator' => $this->language->get('text_separator')
		);

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_quantity'] = $this->language->get('text_quantity');
		$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['text_model'] = $this->language->get('text_model');
		$this->data['text_price'] = $this->language->get('text_price');
		$this->data['text_tax'] = $this->language->get('text_tax');
		$this->data['text_points'] = $this->language->get('text_points');
		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');		
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');

		$this->data['button_cart'] = $this->language->get('button_cart');	
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['compare'] = $this->url->link('product/compare');

		$this->data['products'] = array();
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);
		$product_total = $this->model_catalog_product->getTotalProductLastPieces($data);
		$results = $this->model_catalog_product->getProductLastPieces($data);
		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			} else {
				$image = false;
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$priceB = $this->currency->format($this->tax->calculate(-$result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				$price = $this->currency->format($result['price']);
			} else {
				$priceB = false;
				$price = false;
			}
			if ((float)$result['special']) {
				$specialB = $this->currency->format($this->tax->calculate(-$result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				$special = $this->currency->format($result['special']);
			} else {
				$specialB = false;
				$special = false;
			}
			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float)$special ? $result['special'] : $result['price']);
			} else {
				$tax = false;
			}
			if ($this->config->get('config_review_status')) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}
			$this->data['products'][] = array(
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => $result['name'],
				'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
				'priceB'       => $priceB,
				'price'       => $price,
				'specialB'     => $specialB,
				'special'     => $special,
				'tax'         => $tax,
				'rating'      => $result['rating'],
				'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
			);
		}

		$url = $this->request->_url('limit');
		$this->data['sorts'] = array();
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link('product/lastpieces', 'sort=p.sort_order&order=ASC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'pd.name-ASC',
			'href'  => $this->url->link('product/lastpieces', 'sort=pd.name&order=ASC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'pd.name-DESC',
			'href'  => $this->url->link('product/lastpieces', 'sort=pd.name&order=DESC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_price_asc'),
			'value' => 'ps.price-ASC',
			'href'  => $this->url->link('product/lastpieces', 'sort=ps.price&order=ASC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_price_desc'),
			'value' => 'ps.price-DESC',
			'href'  => $this->url->link('product/lastpieces', 'sort=ps.price&order=DESC' . $url)
		);
		if ($this->config->get('config_review_status')) {	
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->url->link('product/lastpieces', 'sort=rating&order=DESC' . $url)
			); 
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->url->link('product/lastpieces', 'sort=rating&order=ASC' . $url)
			);
		}
		$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/lastpieces', 'sort=p.model&order=ASC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_model_desc'),
			'value' => 'p.model-DESC',
			'href'  => $this->url->link('product/lastpieces', 'sort=p.model&order=DESC' . $url)
		);

        $url = $this->request->_urls(array(
            'sort', 'order'
        ));
		$this->data['limits'] = array();
		$limits = array_unique(array($this->config->get('config_catalog_limit'), 25, 50, 75, 100));
		sort($limits);
		foreach($limits as $value){
			$this->data['limits'][] = array(
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link('product/lastpieces', $url . '&limit=' . $value)
			);
		}

        $url = $this->request->_urls(array(
            'sort', 'order', 'limit'
        ));
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('product/lastpieces', $url . '&page={page}');
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;

		$this->children = array('common/footer','common/header');
        $this->setTemplate('product', 'lastpieces');
		$this->response->setOutput($this->render());			
	}
}