// sortBy composable function
export const sortBy = (columns) => {
  // Use reduce to build an array of fields to sort by
  return columns.reduce((acc, o) => {
    // Check if the column has a sort property
    if (o.sort) {
      // Add the field to the array with or without the '-' prefix
      acc.push(o.sort === 'asc' ? o.field : '-' + o.field);
    }
    // Return the accumulator for the next iteration
    return acc;
  }, []);
};

// onColumnSort composable function
export const onColumnSort = (event, i, columns) => {
  // Get the current column to be sorted
  const sortEl = columns[i];

  // Check if the shift key is pressed
  const isShiftKeyPressed = event.shiftKey;

  // Reset the sort property for other columns
  columns.forEach((o) => {
    if (o.field !== sortEl.field) {
      o.sort = '';
    }
  });

  // Determine the new sort value for the current column
  if (!sortEl.sort) {
    sortEl.sort = 'asc';
  } else if (sortEl.sort === 'desc') {
    sortEl.sort = isShiftKeyPressed ? '' : 'asc';
  } else if (sortEl.sort === 'asc') {
    sortEl.sort = 'desc';
  }
};
