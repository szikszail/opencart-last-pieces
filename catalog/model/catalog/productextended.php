<?php
class ModelCatalogProductextended extends Model {

	public function getProductLastPieces($data = array()) {
		$this->load->model('catalog/product');

		$sql = "SELECT DISTINCT p.product_id, (
					SELECT AVG(rating)
					FROM " . DB_PREFIX . "review r1
					WHERE r1.product_id = p.product_id AND r1.status = '1'
					GROUP BY r1.product_id
				) AS rating
				FROM " . DB_PREFIX . "product p
					LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
					LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				WHERE p.status = '1'
					AND p.date_available <= NOW()
					AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					AND p.quantity < 1
				GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) { 		
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getTotalProductLastPieces() {
		$query = $this->db->query("
			SELECT COUNT(DISTINCT p.product_id) AS total
			FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				WHERE p.status = '1'
					AND p.date_available <= NOW()
					AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					AND p.quantity < 1");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;	
		}
	}
}
?>