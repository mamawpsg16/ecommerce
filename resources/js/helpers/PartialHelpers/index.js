// Function to perform a deep clone of an object or array
export const deepClone = function(obj) {
  if (obj === null || typeof obj !== 'object') {
    return obj;
  }

  const copy = Array.isArray(obj) ? [] : {};

  // Use Object.keys to iterate over object properties
  Object.keys(obj).forEach(key => {
    // Recursively deep clone each property and assign it to the copy
    copy[key] = deepClone(obj[key]);
  });

  return copy;
}


export const generateUniqueSlug = function(name) {
  // Remove special characters, replace spaces with dashes, and convert to lowercase
  const formattedShopName = name.replace(/[^\w\s]/g, '').replace(/\s+/g, '-').toLowerCase();

  // Generate a random alphanumeric string for uniqueness
  const uniqueIdentifier = Math.random().toString(36).substring(2, 8);

  // Combine the name and unique identifier
  const uniqueSlug = `${formattedShopName}-${uniqueIdentifier}`;

  return uniqueSlug;
};


  