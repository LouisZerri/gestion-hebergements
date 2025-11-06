'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import {
  Box,
  Button,
  Container,
  Heading,
  Input,
  Table,
  Text,
  Spinner,
  HStack,
  VStack,
  Image,
  Badge,
  Flex,
} from '@chakra-ui/react';
import { apiService } from '@/lib/api';
import { showToast } from '@/lib/toast';
import type { Hotel, PaginatedResponse } from '@/types/api';

export default function HomePage() {
  const router = useRouter();
  
  const [hotels, setHotels] = useState<Hotel[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [pagination, setPagination] = useState<PaginatedResponse<Hotel> | null>(null);
  const [currentPage, setCurrentPage] = useState(1);

  // Charger les hôtels
  const loadHotels = async (page = 1, search = '') => {
    setLoading(true);
    try {
      let response;
      
      if (search) {
        response = await apiService.searchHotels(search, page);
      } else {
        response = await apiService.getHotels({ page, per_page: 5 });
      }

      if (response.success && response.data) {
        setHotels(response.data.data);
        setPagination(response.data);
      }
    } catch (error: any) {
      showToast.error(error.message || 'Impossible de charger les hôtels');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadHotels(currentPage, searchQuery);
  }, [currentPage]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
    loadHotels(1, searchQuery);
  };

  const handleViewDetails = (id: number) => {
    router.push(`/hotels/${id}`);
  };

  const handleCreateNew = () => {
    router.push('/hotels/new');
  };

  if (loading && hotels.length === 0) {
    return (
      <Container maxW="7xl" py={10}>
        <Flex justify="center" align="center" h="400px">
          <Spinner size="xl" color="blue.500" />
        </Flex>
      </Container>
    );
  }

  return (
    <Container maxW="7xl" py={8}>
      <VStack gap={6} align="stretch">
        {/* Header */}
        <Flex justify="space-between" align="center">
          <Heading size="lg">Gestion des Hébergements</Heading>
          <Button colorScheme="blue" onClick={handleCreateNew}>
            + Nouvel Hôtel
          </Button>
        </Flex>

        {/* Barre de recherche */}
        <Box as="form" onSubmit={handleSearch}>
          <HStack>
            <Input
              placeholder="Rechercher par nom ou ville..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              size="lg"
            />
            <Button type="submit" colorScheme="blue" size="lg" px={8}>
              Rechercher
            </Button>
            {searchQuery && (
              <Button
                variant="outline"
                size="lg"
                onClick={() => {
                  setSearchQuery('');
                  setCurrentPage(1);
                  loadHotels(1, '');
                }}
              >
                Réinitialiser
              </Button>
            )}
          </HStack>
        </Box>

        {/* Tableau des hôtels */}
        <Box overflowX="auto" borderWidth="1px" borderRadius="lg">
          <Table.Root variant="outline" size="lg">
            <Table.Header>
              <Table.Row bg="gray.50">
                <Table.ColumnHeader>Photo</Table.ColumnHeader>
                <Table.ColumnHeader>Nom</Table.ColumnHeader>
                <Table.ColumnHeader>Ville</Table.ColumnHeader>
                <Table.ColumnHeader>Capacité</Table.ColumnHeader>
                <Table.ColumnHeader>Prix/Nuit</Table.ColumnHeader>
                <Table.ColumnHeader>Actions</Table.ColumnHeader>
              </Table.Row>
            </Table.Header>
            <Table.Body>
              {hotels.length === 0 ? (
                <Table.Row>
                  <Table.Cell colSpan={6} textAlign="center" py={10}>
                    <Text color="gray.500">
                      {searchQuery
                        ? 'Aucun hôtel trouvé pour cette recherche'
                        : 'Aucun hôtel disponible'}
                    </Text>
                  </Table.Cell>
                </Table.Row>
              ) : (
                hotels.map((hotel) => (
                  <Table.Row key={hotel.id}>
                    <Table.Cell>
                      {hotel.pictures && hotel.pictures.length > 0 ? (
                        <Image
                          src={apiService.getImageUrl(hotel.pictures[0].filepath)}
                          alt={hotel.name}
                          boxSize="60px"
                          objectFit="cover"
                          borderRadius="md"
                        />
                      ) : (
                        <Box
                          boxSize="60px"
                          bg="gray.200"
                          borderRadius="md"
                          display="flex"
                          alignItems="center"
                          justifyContent="center"
                        >
                          <Text fontSize="xs" color="gray.500">
                            Pas de photo
                          </Text>
                        </Box>
                      )}
                    </Table.Cell>
                    <Table.Cell>
                      <Text fontWeight="medium">{hotel.name}</Text>
                      <Text fontSize="sm" color="gray.600" lineClamp={1}>
                        {hotel.description || 'Pas de description'}
                      </Text>
                    </Table.Cell>
                    <Table.Cell>
                      <Text>{hotel.city}</Text>
                      <Text fontSize="sm" color="gray.600">
                        {hotel.country}
                      </Text>
                    </Table.Cell>
                    <Table.Cell>
                      <Badge colorPalette="purple">{hotel.max_capacity} pers.</Badge>
                    </Table.Cell>
                    <Table.Cell>
                      <Text fontWeight="bold" color="green.600">
                        {hotel.price_per_night.toFixed(2)} €
                      </Text>
                    </Table.Cell>
                    <Table.Cell>
                      <Button
                        size="sm"
                        colorScheme="blue"
                        variant="outline"
                        onClick={() => handleViewDetails(hotel.id)}
                      >
                        Détails
                      </Button>
                    </Table.Cell>
                  </Table.Row>
                ))
              )}
            </Table.Body>
          </Table.Root>
        </Box>

        {/* Pagination */}
        {pagination && pagination.last_page > 1 && (
          <Flex justify="center" gap={2}>
            <Button
              onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
              disabled={currentPage === 1 || loading}
            >
              Précédent
            </Button>
            
            <HStack>
              {Array.from({ length: Math.min(pagination.last_page, 10) }, (_, i) => i + 1).map((page) => (
                <Button
                  key={page}
                  onClick={() => setCurrentPage(page)}
                  colorScheme={currentPage === page ? 'blue' : 'gray'}
                  variant={currentPage === page ? 'solid' : 'outline'}
                  disabled={loading}
                >
                  {page}
                </Button>
              ))}
            </HStack>

            <Button
              onClick={() => setCurrentPage((p) => Math.min(pagination.last_page, p + 1))}
              disabled={currentPage === pagination.last_page || loading}
            >
              Suivant
            </Button>
          </Flex>
        )}

        {/* Info pagination */}
        {pagination && (
          <Text textAlign="center" color="gray.600" fontSize="sm">
            Affichage de {pagination.from} à {pagination.to} sur {pagination.total} hôtels
          </Text>
        )}
      </VStack>
    </Container>
  );
}