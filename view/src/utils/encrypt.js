import crypto from 'crypto'

/**
 * @param str
 * @returns {string}
 */
export function md5(str) {
  return crypto.createHash('md5').update(str).digest('hex')
}
