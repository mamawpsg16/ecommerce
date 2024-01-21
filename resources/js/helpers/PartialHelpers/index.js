// Function to perform a deep clone of an object or array
export const deepClone = function(obj) {
    // Base case: if obj is null or not of type 'object', return obj as it is
    if (obj === null || typeof obj !== 'object') {
      return obj;
    }
    
    // Create a new copy, checking if obj is an array or object
    const copy = Array.isArray(obj) ? [] : {};
    
    // Iterate over all properties of the obj
    for (let key in obj) {
      // Recursively deep clone each property and assign it to the copy
      copy[key] = deepClone(obj[key]);
    }
  
    // Return the cloned object or array
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


  