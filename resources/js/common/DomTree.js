/**
 * Парсинг DOM-элементов и их значений. Пример использования:
 *
 * const DomTree = require('DomTree');
 *
 * const $button = DomTree.getObjectBySelector(
 *      '#browse-button',
 *      'an error occurred while retrieving the "Browse button" object'
 * );
 *
 * const $buttonsList = DomTree.getAllObjectsBySelector(
 *      '#drop-button',
 *      'an error occurred while retrieving the "Drop button" object'
 * );
 *
 * const url = DomTree.getValueBySelector(
 *      'input[name=_upload_url]',
 *      'an error occurred while retrieving the upload URL value'
 * );
 */
const DomTreeException = require('../exceptions/DomTreeException');

/**
 * Возвращает DOM-элемент на основе переданного селектора.
 *
 * @param {string} selector
 * @param {string} exceptMsg
 * @param {Document|Element} $object
 * @returns {*}
 */
export function getObjectBySelector(
    selector,
    exceptMsg,
    $object = document,
) {

    const s = validateParam(selector, 'selector');
    const msg = validateParam(exceptMsg, 'exceptMsg');

    const $obj = $object.querySelector(s);

    if (!$obj) {
        throw new DomTreeException(msg);
    }

    return $obj;

}

/**
 * Возвращает коллекцию DOM-элементов на основе переданного селектора.
 *
 * @param {string} selector
 * @param {string} exceptMsg
 * @param {Document|Element} $object
 * @return {NodeListOf<*>}
 */
export function getAllObjectsBySelector(
    selector,
    exceptMsg,
    $object = document,
) {

    const s = validateParam(selector, 'selector');
    const msg = validateParam(exceptMsg, 'exceptMsg');

    const $objList = $object.querySelectorAll(s);

    if (!$objList.length) {
        throw new DomTreeException(msg);
    }

    return $objList;

}

/**
 * Возвращает значение DOM-элемента (value) на основе переданного селектора.
 *
 * @param {string} selector
 * @param {string} exceptMsg
 * @param {Document|Element} $object
 * @returns {*}
 */
export function getValueBySelector(
    selector,
    exceptMsg,
    $object = document,
) {

    const $select = this.getObjectBySelector(selector, exceptMsg, $object);

    return $select.value.trim();

}

/**
 * @param {string} param
 * @param {string} paramName
 * @return {*}
 */
function validateParam(param, paramName) {

    const result = param.trim();

    if (!result) {
        throw new DomTreeException(
            'the "' + paramName + '" parameter was not specified');
    }

    return result;

}
