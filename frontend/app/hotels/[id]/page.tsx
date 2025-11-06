'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import {
  Box,
  Button,
  Container,
  Heading,
  Text,
  Spinner,
  HStack,
  VStack,
  Image,
  Badge,
  Flex,
  Grid,
  SimpleGrid,
  Separator,
  IconButton,
} from '@chakra-ui/react';
import { apiService } from '@/lib/api';
import { showToast } from '@/lib/toast';
import type { Hotel } from '@/types/api';

export default function HotelDetailsPage() {
  const params = useParams();
  const router = useRouter();
  const hotelId = Number(params.id);

  const [hotel, setHotel] = useState<Hotel | null>(null);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState<string | null>(null);
  const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
  const [deleting, setDeleting] = useState(false);

  // Charger les détails de l'hôtel
  const loadHotel = async () => {
    setLoading(true);
    try {
      const response = await apiService.getHotel(hotelId);
      if (response.success && response.data) {
        setHotel(response.data);
        if (response.data.pictures && response.data.pictures.length > 0) {
          setSelectedImage(apiService.getImageUrl(response.data.pictures[0].filepath));
        }
      }
    } catch (error: any) {
      showToast.error(error.message || 'Impossible de charger l\'hôtel');
      router.push('/');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadHotel();
  }, [hotelId]);

  const handleDelete = async () => {
    setDeleting(true);
    try {
      await apiService.deleteHotel(hotelId);
      showToast.success('Hôtel supprimé avec succès');
      router.push('/');
    } catch (error: any) {
      showToast.error(error.message || 'Impossible de supprimer l\'hôtel');
      setDeleting(false);
    }
  };

  const handleEdit = () => {
    router.push(`/hotels/${hotelId}/edit`);
  };

  const handleBack = () => {
    router.push('/');
  };

  if (loading) {
    return (
      <Container maxW="7xl" py={10}>
        <Flex justify="center" align="center" h="400px">
          <Spinner size="xl" color="blue.500" />
        </Flex>
      </Container>
    );
  }

  if (!hotel) {
    return (
      <Container maxW="7xl" py={10}>
        <Text>Hôtel non trouvé</Text>
      </Container>
    );
  }

  return (
    <Container maxW="7xl" py={8}>
      <VStack gap={6} align="stretch">
        {/* Navigation */}
        <HStack justify="space-between">
          <Button variant="outline" onClick={handleBack}>
            ← Retour
          </Button>
          <HStack>
            <Button colorScheme="blue" onClick={handleEdit}>
              Modifier
            </Button>
            {!showDeleteConfirm ? (
              <Button
                colorScheme="red"
                variant="outline"
                onClick={() => setShowDeleteConfirm(true)}
              >
                Supprimer
              </Button>
            ) : (
              <HStack bg="red.50" p={2} borderRadius="md" borderWidth="1px" borderColor="red.200">
                <Text fontSize="sm" color="red.700">Confirmer ?</Text>
                <Button
                  size="sm"
                  colorScheme="red"
                  onClick={handleDelete}
                  loading={deleting}
                >
                  Oui
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => setShowDeleteConfirm(false)}
                >
                  Non
                </Button>
              </HStack>
            )}
          </HStack>
        </HStack>

        {/* Contenu principal */}
        <Grid templateColumns={{ base: '1fr', lg: '1fr 1fr' }} gap={8}>
          {/* Galerie photos */}
          <VStack gap={4} align="stretch">
            {/* Image principale */}
            <Box
              borderWidth="1px"
              borderRadius="lg"
              overflow="hidden"
              bg="gray.100"
              h="400px"
              display="flex"
              alignItems="center"
              justifyContent="center"
            >
              {selectedImage ? (
                <Image
                  src={selectedImage}
                  alt={hotel.name}
                  objectFit="cover"
                  w="full"
                  h="full"
                />
              ) : (
                <Text color="gray.500">Aucune photo disponible</Text>
              )}
            </Box>

            {/* Miniatures */}
            {hotel.pictures && hotel.pictures.length > 0 && (
              <SimpleGrid columns={4} gap={2}>
                {hotel.pictures.map((picture) => {
                  const imageUrl = apiService.getImageUrl(picture.filepath);
                  return (
                    <Box
                      key={picture.id}
                      borderWidth="2px"
                      borderColor={selectedImage === imageUrl ? 'blue.500' : 'transparent'}
                      borderRadius="md"
                      overflow="hidden"
                      cursor="pointer"
                      onClick={() => setSelectedImage(imageUrl)}
                      transition="all 0.2s"
                      _hover={{ borderColor: 'blue.300' }}
                    >
                      <Image
                        src={imageUrl}
                        alt={`${hotel.name} - ${picture.position}`}
                        objectFit="cover"
                        w="full"
                        h="80px"
                      />
                    </Box>
                  );
                })}
              </SimpleGrid>
            )}
          </VStack>

          {/* Informations */}
          <VStack gap={6} align="stretch">
            {/* Titre et badges */}
            <Box>
              <Heading size="xl" mb={2}>{hotel.name}</Heading>
              <HStack>
                <Badge colorPalette="purple" size="lg">
                  {hotel.max_capacity} personnes max
                </Badge>
                <Badge colorPalette="green" size="lg">
                  {hotel.price_per_night.toFixed(2)} € / nuit
                </Badge>
              </HStack>
            </Box>

            <Separator />

            {/* Adresse */}
            <Box>
              <Text fontWeight="bold" mb={2} fontSize="lg">📍 Adresse</Text>
              <VStack align="stretch" gap={1}>
                <Text>{hotel.address_1}</Text>
                {hotel.address_2 && <Text>{hotel.address_2}</Text>}
                <Text>{hotel.zip_code} {hotel.city}</Text>
                <Text fontWeight="medium">{hotel.country}</Text>
              </VStack>
            </Box>

            <Separator />

            {/* Coordonnées */}
            <Box>
              <Text fontWeight="bold" mb={2} fontSize="lg">🗺️ Coordonnées GPS</Text>
              <HStack>
                <Badge>Lat: {hotel.latitude}</Badge>
                <Badge>Lng: {hotel.longitude}</Badge>
              </HStack>
            </Box>

            <Separator />

            {/* Description */}
            {hotel.description && (
              <Box>
                <Text fontWeight="bold" mb={2} fontSize="lg">📝 Description</Text>
                <Text color="gray.700" lineHeight="tall">
                  {hotel.description}
                </Text>
              </Box>
            )}

            {/* Métadonnées */}
            <Box bg="gray.50" p={4} borderRadius="md">
              <Text fontSize="sm" color="gray.600">
                Créé le : {new Date(hotel.created_at).toLocaleDateString('fr-FR')}
              </Text>
              <Text fontSize="sm" color="gray.600">
                Modifié le : {new Date(hotel.updated_at).toLocaleDateString('fr-FR')}
              </Text>
            </Box>
          </VStack>
        </Grid>
      </VStack>
    </Container>
  );
}