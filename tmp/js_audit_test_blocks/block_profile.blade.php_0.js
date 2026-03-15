
                            function togglePostal(isSame) {
                                const field = document.getElementById('postalField');
                                const input = document.getElementById('postal_address_input');
                                if (isSame) {
                                    field.style.display = 'none';
                                    input.removeAttribute('required');
                                } else {
                                    field.style.display = 'block';
                                    input.setAttribute('required', 'required');
                                }
                            }
                        