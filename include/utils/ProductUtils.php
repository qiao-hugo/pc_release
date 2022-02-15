<?php
/**
 * 父类和子类产品合并
 * @param mix $productResult
 * @param mix $subproduct
 */
function subproducts($productResult, $subproduct) {
	$productCombo = array();
	$result = array();
	if(!empty($productResult)){
		if (! empty ( $subproduct )) {
			foreach ( $subproduct as $subval ) {
				$productCombo = array (
						'productid' => $productResult ['id'],
						'productcomboid' => $productResult ['id'],
						'productcomboname' => $productResult ['productname'] 
				);
				$temp = $subval->getData();
				$result [$temp['id']] = array_merge ( $temp, $productCombo );
			}
		} else {
			$productCombo = array (
					'productid' => $productResult ['id'],
					'productcomboid' => 0,
					'productcomboname' => '' 
			);
			$result [$productResult['id']] = array_merge ( $productResult, $productCombo );
		}
	}
	return $result;
}